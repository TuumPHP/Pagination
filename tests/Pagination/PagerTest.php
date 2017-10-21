<?php
namespace tests\Pagination;

use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class PagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pager
     */
    private $pager;

    function setup()
    {
        $this->pager   = new Pager();
        $_SESSION = [];
    }

    function test0()
    {
        $this->assertEquals('Tuum\Pagination\Pager', get_class($this->pager));
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

    /**
     * thanks:
     * http://stackoverflow.com/questions/1301402/example-invalid-utf8-string
     *
     * @test
     */
    function check_for_invalid_inputs()
    {
        $bad = array(
            'null' => "ab\0cd",
            'Valid ASCII' => "a",
            'Valid 2 Octet Sequence' => "\xc3\xb1",
            'Invalid 2 Octet Sequence' => "\xc3\x28",
            'Invalid Sequence Identifier' => "\xa0\xa1",
            'Valid 3 Octet Sequence' => "\xe2\x82\xa1",
            'Invalid 3 Octet Sequence (in 2nd Octet)' => "\xe2\x28\xa1",
            'Invalid 3 Octet Sequence (in 3rd Octet)' => "\xe2\x82\x28",
            'Valid 4 Octet Sequence' => "\xf0\x90\x8c\xbc",
            'Invalid 4 Octet Sequence (in 2nd Octet)' => "\xf0\x28\x8c\xbc",
            'Invalid 4 Octet Sequence (in 3rd Octet)' => "\xf0\x90\x28\xbc",
            'Invalid 4 Octet Sequence (in 4th Octet)' => "\xf0\x28\x8c\x28",
            'Valid 5 Octet Sequence (but not Unicode!)' => "\xf8\xa1\xa1\xa1\xa1",
            'Valid 6 Octet Sequence (but not Unicode!)' => "\xfc\xa1\xa1\xa1\xa1\xa1",
        );

        $this->pager->withQuery($bad, '/test')->call(function(Inputs $inputs) {
            $this->assertEquals('', $inputs->get('null'));
            if (!$inputs->get('Valid ASCII')) {
                throw new \RuntimeException('must receive valid ascii input');
            }
            $this->assertEquals('', $inputs->get('Invalid Sequence Identifier'));
            $this->assertEquals('', $inputs->get('Invalid 4 Octet Sequence (in 3rd Octet)'));
        });
    }

    /**
     * @test
     */
    function using_own_validator()
    {
        $pager = $this->pager;
        $pager->useValidator(function(&$v) {
            $v = 'tested:'.$v;
        });
        $pager->withQuery(['test' => 'done'], '/test')->call(function(Inputs $inputs) {
            $this->assertEquals('tested:done', $inputs->get('test'));
        });

    }
}