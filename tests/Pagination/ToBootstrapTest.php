<?php
namespace tests\Pagination;

use Tuum\Respond\RequestHelper;
use WScore\Pagination\Html\AbstractPaginate;
use WScore\Pagination\Html\Paginate;
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;

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
        $inputs->paginate(new Paginate());
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"previous page\" >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"next page\" >next</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' aria-label=\"last page\" >&raquo;</a></li>", $html);
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
        $pages = $inputs->paginate(new Paginate());
        $html  = $pages->__toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"previous page\" >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"next page\" >next</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"last page\" >&raquo;</a></li>", $html);
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
        /** @var AbstractPaginate $pages */
        $pages = $inputs->paginate(new Paginate());
        $pages->default_type = 'none';
        $html  = $pages->__toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"previous page\" >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
    }
}
