<?php
namespace tests\Pagination;

use Tuum\Respond\RequestHelper;
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pager
     */
    private $pager;

    function setup()
    {
        $this->pager   = new Pager();
    }

    function test0()
    {
        $this->assertEquals('WScore\Pagination\Pager', get_class($this->pager));
    }

    /**
     * @param string $path
     * @param string $method
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    function createRequest($path, $method = 'get')
    {
        $req = RequestHelper::createFromPath($path, $method);
        return $req;
    }

    /**
     * @test
     */
    function withRequest_is_immutable_method()
    {
        $req   = $this->createRequest('/test/');
        $pager = $this->pager->withRequest($req);
        $this->assertNotSame($this->pager, $pager);
    }

    /**
     * @test
     */
    function values_in_query_appears_in_Inputs()
    {
        $req   = $this->createRequest('/test/')->withQueryParams([
            'test' => 'tested',
            'more' => 'done',
        ]);
        $pager = $this->pager->withRequest($req);
        /** @var Inputs $inputs */
        $inputs = $pager->call(function (Inputs $inputs) {
            $inputs->setTotal(123);
            $inputs->get('none', 'yes');
            $inputs->setList(['more' => 'test']);
        });
        $this->assertEquals('tested', $inputs->get('test'));
        $this->assertEquals('done', $inputs->get('more'));
        $this->assertEquals('yes', $inputs->get('none'));
        $this->assertEquals('1', $inputs->getPage());
        $this->assertEquals('20', $inputs->getLimit());
        $this->assertEquals('0', $inputs->getOffset());
        $this->assertEquals(123, $inputs->getTotal());
        $this->assertEquals(1, $inputs->getCount());
        $this->assertEquals(['more' => 'test'], $inputs->getList());
    }

    /**
     * @test
     */
    function second_request_with_page_keeps_queries()
    {
        // first request.
        $req   = $this->createRequest('/test/')->withQueryParams([
            'test' => 'tested',
            'more' => 'done',
        ]);
        $this->pager->withRequest($req);

        // second request. uses the same session segment.
        $req   = $this->createRequest('/test/')->withQueryParams(['_page' => 2]);
        $pager = new Pager();
        $pager = $pager->withRequest($req);
        $inputs = $pager->call(function (Inputs $inputs) {
            $inputs->setTotal(123);
        });
        $this->assertEquals('tested', $inputs->get('test'));
        $this->assertEquals('done', $inputs->get('more'));
        $this->assertEquals('2', $inputs->getPage());
        $this->assertEquals('20', $inputs->getLimit());
        $this->assertEquals('20', $inputs->getOffset());
        $this->assertEquals(123, $inputs->getTotal());
        $this->assertEquals(0, $inputs->getCount());
    }

    /**
     * @test
     */
    function third_request_with_only_page_keeps_previous_page()
    {
        // first request.
        $req   = $this->createRequest('/test/')->withQueryParams([
            'test' => 'tested',
            'more' => 'done',
        ]);
        $this->pager->withRequest($req);

        // second request. uses the same session segment.
        $req   = $this->createRequest('/test/')->withQueryParams(['_page' => 2]);
        (new Pager())->withRequest($req);

        // third request.
        $req   = $this->createRequest('/test/')->withQueryParams(['_page' => null]);
        $pager = (new Pager())->withRequest($req);

        $inputs = $pager->call(function (Inputs $inputs) {
            $inputs->setTotal(123);
        });
        $this->assertEquals('tested', $inputs->get('test'));
        $this->assertEquals('done', $inputs->get('more'));
        $this->assertEquals('2', $inputs->getPage());
        $this->assertEquals('2', $inputs->calcSelfPage());
        $this->assertEquals('20', $inputs->getLimit());
        $this->assertEquals('20', $inputs->getOffset());
        $this->assertEquals(123, $inputs->getTotal());
    }

    /**
     * @test
     */
    function withQuery_third_request_with_only_page_keeps_previous_page()
    {
        // first request.
        $this->pager->withQuery([
            'test' => 'tested',
            'more' => 'done',
        ], '/test/');

        // second request. uses the same session segment.
        (new Pager())->withQuery(['_page' => 2], '/test/');

        // third request.
        $pager = (new Pager())->withQuery(['_page' => null], '/test/');

        $inputs = $pager->call(function (Inputs $inputs) {
            $inputs->setTotal(123);
        });
        $this->assertEquals('tested', $inputs->get('test'));
        $this->assertEquals('done', $inputs->get('more'));
        $this->assertEquals('2', $inputs->getPage());
        $this->assertEquals('20', $inputs->getLimit());
        $this->assertEquals('20', $inputs->getOffset());
        $this->assertEquals(123, $inputs->getTotal());
    }
}