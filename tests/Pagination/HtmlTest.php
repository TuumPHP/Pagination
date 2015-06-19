<?php
namespace tests\Pagination;

use tests\Utils\Segment;
use Tuum\Respond\RequestHelper;
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;
use WScore\Pagination\Html\ToBootstrap;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function get_bootstrap_style_html()
    {
        $session = new Segment();
        $req = RequestHelper::createFromPath('/test');
        $req = RequestHelper::withSessionMgr($req, $session);

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
            return $inputs;
        });
        $pages = $pager->toHtml(new ToBootstrap());
        $html  = $pages->__toString();
        $this->assertContains("<li><a href='/test?_page=1' >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' >prev</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >next</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' >&raquo;</a></li>", $html);
    }
}
