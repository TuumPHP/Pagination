<?php
namespace tests\Html;

use Tuum\Pagination\Paginate\PaginateMini;
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
        $lists = $pages->toArray();
        $this->assertEquals(['rel' => 'first', 'href' => '/test?_page=1', 'aria' => 'first page'], $lists[0]);
        $this->assertEquals(['rel' => '2', 'href' => '/test?_page=2', 'aria' => ''], $lists[1]);
        $this->assertEquals(['rel' => '4', 'href' => '#', 'aria' => ''], $lists[3]);
        $this->assertEquals(['rel' => '6', 'href' => '/test?_page=6', 'aria' => ''], $lists[5]);
        $this->assertEquals(['rel' => 'last', 'href' => '/test?_page=10', 'aria' => 'last page'], $lists[6]);
    }
}
