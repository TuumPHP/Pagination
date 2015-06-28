<?php
namespace tests\Pagination;

use Tuum\Respond\RequestHelper;
use WScore\Pagination\Html\ToBootstrapNext;
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;

class ToBootstrapNextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function get_bootstrap_style_html()
    {
        $pager = (new Pager())->withQuery(['_page' => 4], '/test');
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $inputs->toHtml(new ToBootstrapNext());
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >1</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=5' aria-label=\"next page\" >next</a></li>", $html);
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
        $inputs->toHtml(new ToBootstrapNext(['num_links' => 2]));
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=5' aria-label=\"next page\" >next</a></li>", $html);
    }
}
