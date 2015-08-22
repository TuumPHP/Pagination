<?php
namespace tests\Pagination;

use Tuum\Pagination\Html\PaginateMini;
use Tuum\Pagination\Inputs;

class PaginateMiniTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaginateMini
     */
    private $mini;

    function setup()
    {
        $this->mini = new PaginateMini();
    }
    
    function test0()
    {
        $this->assertEquals('Tuum\Pagination\Html\PaginateMini', get_class($this->mini));
    }

    /**
     * @test
     */
    function generic()
    {
        $inputs = new Inputs();
        $inputs->inputs = [
            '_page' => 5,
            '_limit' => 10,
        ];
        $inputs->setTotal(100);
        $m = $this->mini->withInputs($inputs)->numLinks(2);
        $a = $m->toArray();
        $this->assertEquals('prev', $a[0]['rel']);
        $this->assertEquals('first', $a[1]['rel']);
        $this->assertEquals([], $a[2]);
    }
}