<?php
namespace tests\Pagination;

use Tuum\Pagination\Factory\PageJa;
use Tuum\Pagination\Factory\Pagination;
use Tuum\Pagination\Html\Paginate;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class ToBootstrapJaTest extends \PHPUnit_Framework_TestCase
{
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
        $pager = PageJa::forge()->numLinks(2)->getPager();
        $pager = $pager->withQuery(['_page' => 4], '/test');
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $html  = $inputs->__toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >≪</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"previous page\" >前</a></li>", $html);
        $this->assertNotContains("<li><a href='/test?_page=1' >1</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertNotContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=5' aria-label=\"next page\" >次</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=14' aria-label=\"last page\" >≫</a></li>", $html);
    }
}
