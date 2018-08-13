<?php
namespace tests\Pagination;

use Tuum\Pagination\Inputs;

class InputsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Inputs
     */
    private $inputs;

    function setup()
    {
        $this->inputs = new Inputs();
    }

    function test0()
    {
        $this->assertEquals('Tuum\Pagination\Inputs', get_class($this->inputs));
    }

    /**
     * @test
     */
    function inputs_various_values()
    {
        $this->inputs->inputs = [
            '_page' => 3,
            '_limit' => 10,
            'test' => 'tested',
        ];
        $this->inputs->setTotal(100);
        $this->inputs->path = 'testing';
        
        $i = $this->inputs;
        $this->assertEquals(3, $i->getPage());
        $this->assertEquals(0, $i->getCount());
        $this->assertEquals(20, $i->getOffset());
        $this->assertEquals(10, $i->getLimit());
        $this->assertEquals(100, $i->getTotal());
        $this->assertEquals('tested', $i->get('test'));
        $this->assertEquals('done', $i->get('more', 'done'));
        $this->assertEquals('testing?_page=2', $i->getPath(2));
    }

    /**
     * @test
     */
    function list_and_counts()
    {
        $list = [
            'a', 'b', 'c'
        ];
        $this->inputs->setList($list);

        $i = $this->inputs;
        $this->assertEquals(3, $i->getCount());
        $this->assertEquals(3, $i->getCount());
        $this->assertEquals($list, $i->getList());
    }

    /**
     * @test
     */
    function calc()
    {
        $this->inputs->inputs = [
            '_page' => 3,
            '_limit' => 10,
            'test' => 'tested',
        ];
        $this->inputs->setTotal(100);

        $i = $this->inputs;
        $this->assertEquals(1, $i->calcFirstPage());
        $this->assertEquals(10, $i->calcLastPage());
    }
}