<?php
namespace tests\Pagination;

use Tuum\Respond\RequestHelper;
use WScore\Pagination\Html\AbstractBootstrap;
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;
use WScore\Pagination\Html\ToBootstrap;

class ToBootstrapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function get_bootstrap_style_html()
    {
        $req = RequestHelper::createFromPath('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $inputs->toHtml(new ToBootstrap());
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >next</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' >&raquo;</a></li>", $html);
    }

    /**
     * @test
     */
    function without_total()
    {
        $req = RequestHelper::createFromPath('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $inputs= $pager->call(function(Inputs $inputs) {
        });
        $pages = $inputs->toHtml(new ToBootstrap());
        $html  = $pages->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >next</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >&raquo;</a></li>", $html);
    }

    /**
     * @test
     */
    function use_default_type()
    {
        $req = RequestHelper::createFromPath('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(35);
        });
        /** @var AbstractBootstrap $pages */
        $pages = $inputs->toHtml(new ToBootstrap());
        $pages->default_type = 'none';
        $html  = $pages->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
    }
}
