<?php
namespace tests\Pagination;

use Tuum\Respond\RequestHelper;
use WScore\Pagination\Html\PaginateMini;
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

        $pager = (new Pager(['_limit'=>5]))->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $inputs->paginate(new PaginateMini());
        $html  = $inputs->__toString();
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=40' aria-label=\"last page\" >&raquo;</a></li>", $html);
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
        $toHtml = new PaginateMini();
        $toHtml->num_links = 2;
        $inputs->paginate($toHtml);
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' aria-label=\"last page\" >&raquo;</a></li>", $html);
    }
}
