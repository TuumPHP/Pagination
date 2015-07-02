<?php
namespace tests\Pagination;

use Tuum\Pagination\Factory\PageJa;
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
        $paginate = PageJa::start()->numLinks(2)->getPaginate();
        $pager = (new Pager(['_limit'=>15]))->withQuery(['_page' => 4], '/test');
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $html  = $inputs->paginate($paginate)->__toString();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"最初のページ\" >≪</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' aria-label=\"前のページ\" >前</a></li>", $html);
        $this->assertNotContains("<li><a href='/test?_page=1' >1</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertNotContains("<li><a href='/test?_page=7' >7</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=5' aria-label=\"次のページ\" >次</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=14' aria-label=\"最後のページ\" >≫</a></li>", $html);
    }
}
