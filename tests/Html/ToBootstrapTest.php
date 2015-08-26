<?php
namespace tests\Html;

use Tuum\Pagination\Html\PaginateFull;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class ToBootstrapTest extends \PHPUnit_Framework_TestCase
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
        $req = $this->createRequest('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 4]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $pages = PaginateFull::forge()->numLinks(2)->withInputs($inputs);
        $html  = ToHtmlBootstrap::forge()->withPaginate($pages)->toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >First</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"previous page\" >&laquo;</a></li>", $html);
        $this->assertNotContains("<li><a href='/test?_page=1' >1</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertNotContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=5' aria-label=\"next page\" >&raquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' aria-label=\"last page\" >Last</a></li>", $html);
    }

    /**
     * @test
     */
    function without_total()
    {
        $req = $this->createRequest('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $inputs= $pager->call(function(Inputs $inputs) {
        });
        $pages = PaginateFull::forge()->withInputs($inputs);
        $html  = ToHtmlBootstrap::forge()->withPaginate($pages)->toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >First</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"previous page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"next page\" >&raquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"last page\" >Last</a></li>", $html);
    }

    /**
     * @test
     */
    function use_default_type()
    {
        $req = $this->createRequest('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 2]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(35);
        });
        $pages = PaginateFull::forge(['first' => '1st page'])->withInputs($inputs);
        $html  = ToHtmlBootstrap::forge(['first' => '1st'])->withPaginate($pages)->toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"1st page\" >1st</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"previous page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >2</a></li>", $html);
    }
}
