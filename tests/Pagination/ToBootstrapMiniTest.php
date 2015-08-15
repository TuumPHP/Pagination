<?php
namespace tests\Pagination;

use Tuum\Pagination\Html\PaginateMini;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class ToBootstrapMiniTest extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        $_SESSION = [];
    }

    /**
     * @param string $path
     * @param string $method
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    function createRequest($path, $method = 'get')
    {
        $req = new ServerRequest([], [], new Uri($path), $method, 'php://input', []);
        return $req;
    }

    /**
     * @test
     */
    function get_bootstrap_style_html()
    {
        $pager = new Pager();
        $pager = $pager->withQuery(['_page' => 2,'_limit'=>5], '/test');
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $pages = PaginateMini::forge()->withInputs($inputs);
        $lists = $pages->toArray();
        $this->assertEquals(['rel' => 1, 'href' => '/test?_page=1', 'aria' => null], $lists[0]);
        $this->assertEquals(['rel' => 2, 'href' => '#', 'aria' => null], $lists[1]);
        $this->assertEquals(['rel' => 'last', 'href' => '/test?_page=40', 'aria' => 'last page'], $lists[11]);
    }

    /**
     * @test
     */
    function get_bootstrap_all()
    {
        $req = $this->createRequest('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 4]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $pages = PaginateMini::forge()->numLinks(2)->withInputs($inputs);
        $html  = ToHtmlBootstrap::forge()->withPaginate($pages)->toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' aria-label=\"last page\" >&raquo;</a></li>", $html);
    }
}
