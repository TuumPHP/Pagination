<?php
namespace tests\Pagination;

use Tuum\Pagination\Html\PaginateMini;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class ToBootstrapMiniTest extends \PHPUnit_Framework_TestCase
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
        $pager = new Pager();
        $pager = $pager->withQuery(['_page' => 2,'_limit'=>5], '/test');
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $paginate = $inputs->paginate(new PaginateMini());
        $html  = $paginate->__toString();
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
        $req = $this->createRequest('/test');

        $pager = (new Pager())->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 4]));
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $toHtml = new PaginateMini();
        $toHtml->num_links = 2;
        $html  = (string) $inputs->paginate($toHtml);
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=2' >2</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=6' >6</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=10' aria-label=\"last page\" >&raquo;</a></li>", $html);
    }
}
