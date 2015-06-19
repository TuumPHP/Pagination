<?php
namespace tests\Pagination;

use WScore\Pagination\Pager;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $pager = new Pager();
        $this->assertEquals('get', get_class($pager));
    }
}