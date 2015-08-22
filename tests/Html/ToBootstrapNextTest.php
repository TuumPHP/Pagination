<?php
namespace tests\Html;

use Tuum\Pagination\Html\PaginateNext;
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
        $lists = $paginate->toArray();
        $this->assertEquals(['rel' => 1, 'href' => '/test?_page=1', 'aria' => null], $lists[0]);
        $this->assertEquals(['rel' => 3, 'href' => '/test?_page=3', 'aria' => null], $lists[2]);
        $this->assertEquals(['rel' => 4, 'href' => '#', 'aria' => null], $lists[3]);
        $this->assertEquals(['rel' => 'next', 'href' => '/test?_page=5', 'aria' => 'next page'], $lists[4]);
    }
}
