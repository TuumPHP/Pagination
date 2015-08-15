<?php
namespace tests\Pagination;

use Tuum\Pagination\Factory\Pagination;
use Tuum\Pagination\Html\Paginate;
use Tuum\Pagination\Html\PaginateNext;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class ToBootstrapNextTest extends \PHPUnit_Framework_TestCase
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
        $pager = (new Pager())->withQuery(['_page' => 4], '/test');
        $inputs= $pager->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        });
        $paginate = PaginateNext::forge()->withInputs($inputs);
        $html  = ToHtmlBootstrap::forge()->withPaginate($paginate)->toString();
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
        $req = $this->createRequest('/test');

        $inputs = new Inputs();
        $pager = (new Pager([], $inputs))->withRequest($req);
        $pager = $pager->withRequest($req->withQueryParams(['_page' => 4]));
        $html = Pagination::forge($pager)->call(function(Inputs $inputs) {
            $inputs->setTotal(200);
        })->toHtml();
        $this->assertContains("<li><a href='/test?_page=1' aria-label=\"first page\" >&laquo;</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=3' >3</a></li>", $html);
        $this->assertContains("<li class='active'><a href='#' >4</a></li>", $html);
        $this->assertContains("<li><a href='/test?_page=5' aria-label=\"next page\" >next</a></li>", $html);
    }
}
