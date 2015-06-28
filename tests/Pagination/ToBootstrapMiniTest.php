<?php
namespace tests\Pagination;

use Tuum\Respond\RequestHelper;
use WScore\Pagination\Html\ToBootstrapMini;
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;

class ToBootstrapMiniTest extends \PHPUnit_Framework_TestCase
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
        $inputs->toHtml(new ToBootstrapMini());
        $html  = $inputs->__toString();
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' >&raquo;</a></li>", $html);
    }

    /**
     * @test
     */
    function get_bootstrap_all()
    {
        $req = RequestHelper::createFromPath('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 4]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $inputs->toHtml(new ToBootstrapMini(['num_links' => 2]));
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >&laquo;</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' >&raquo;</a></li>", $html);
    }
}
