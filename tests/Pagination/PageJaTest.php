<?php
namespace tests\Html;

use Tuum\Pagination\Local\PageJa;
use Tuum\Pagination\Inputs;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class PageJaTest extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        $_SESSION = [];
    }

    /**
     * @param string $path
     * @param string $method
     * @param array  $query
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    function createRequest($path, $method = 'get', $query=[])
    {
        $req = new ServerRequest([], [], new Uri($path), $method, 'php://input', []);
        $req = $req->withQueryParams($query);
        return $req;
    }

    /**
     * @test
     */
    function get_bootstrap_style_html()
    {
        $pages = PageJa::forge();
        $pages->num_links = 3;
        $request = $this->createRequest('/test', 'get', ['_page' => 5]);
        $html = $pages->call($request, function(Inputs $inputs) {
            $inputs->setTotal(200);
        })->toHtml();
        $h = explode("\n", $html);
        $this->assertContains('<li><a href="/test?_page=4" aria-label="前のページ" >&laquo;</a></li>', $h[1]);
        $this->assertContains('<li><a href="/test?_page=1" aria-label="最初のページ" >1</a></li>', $h[2]);
        $this->assertContains('<li class="disable"><a href="#" >...</a></li>', $h[3]);
        $this->assertContains('<li><a href="/test?_page=4" >4</a></li>', $h[4]);
        $this->assertContains('<li><a href="/test?_page=10" aria-label="最後のページ" >10</a></li>', $h[8]);
        $this->assertContains('<li><a href="/test?_page=6" aria-label="次のページ" >&raquo;</a></li>', $h[9]);
    }
}
