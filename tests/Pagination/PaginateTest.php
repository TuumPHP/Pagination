<?php
namespace tests\Pagination;

use Tuum\Pagination\Paginate\Page;
use Tuum\Pagination\Paginate\Paginate;

require_once __DIR__ . '/../autoloader.php';

class PaginateTest extends \PHPUnit\Framework\TestCase
{
    public function testOnly4()
    {
        $list = array(
            [1, 4, '- 2 3 4'],
            [2, 4, '1 - 3 4'],
            [3, 4, '1 2 - 4'],
            [4, 4, '1 2 3 -'],
        );
        foreach($list as $test) {
            $this->assertEquals($test[2], $this->makePageList($test[0], $test[1]));
        }
    }

    public function testOnly7()
    {
        $list = array(
            [1, 7, '- 2 3 4 5 6 7'],
            [2, 7, '1 - 3 4 5 6 7'],
            [3, 7, '1 2 - 4 5 6 7'],
            [6, 7, '1 2 3 4 5 - 7'],
            [7, 7, '1 2 3 4 5 6 -'],
        );
        foreach($list as $test) {
            $this->assertEquals($test[2], $this->makePageList($test[0], $test[1]));
        }
    }

    public function testOnly9()
    {
        $list = array(
            [1, 9, '- 2 3 4 5 6 7 8 9'],
            [4, 9, '1 2 3 - 5 6 7 8 9'],
            [8, 9, '1 2 3 4 5 6 7 - 9'],
            [9, 9, '1 2 3 4 5 6 7 8 -'],
        );
        foreach($list as $test) {
            $this->assertEquals($test[2], $this->makePageList($test[0], $test[1]));
        }
    }

    public function testOnly15()
    {
        $list = array(
            [1, 15, '- 2 3 4 5 6 7 . 15'],
            [2, 15, '1 - 3 4 5 6 7 . 15'],
            [3, 15, '1 2 - 4 5 6 7 . 15'],
            [4, 15, '1 2 3 - 5 6 7 . 15'],
            [5, 15, '1 2 3 4 - 6 7 . 15'],
            [6, 15, '1 . 4 5 - 7 8 . 15'],
            [7, 15, '1 . 5 6 - 8 9 . 15'],
            [8, 15, '1 . 6 7 - 9 10 . 15'],
            [9, 15, '1 . 7 8 - 10 11 . 15'],
            [10, 15, '1 . 8 9 - 11 12 . 15'],
            [11, 15, '1 . 9 10 - 12 13 14 15'],
            [12, 15, '1 . 9 10 11 - 13 14 15'],
            [13, 15, '1 . 9 10 11 12 - 14 15'],
            [14, 15, '1 . 9 10 11 12 13 - 15'],
            [15, 15, '1 . 9 10 11 12 13 14 -'],
        );
        foreach($list as $test) {
            $this->assertEquals($test[2], $this->makePageList($test[0], $test[1]));
        }
    }
    
    /**
     * @param int $currPage
     * @param int $lastPage
     * @return string
     */
    private function makePageList($currPage, $lastPage)
    {
        $p     = new Paginate($currPage, $lastPage);
        $pp = function(Page $page) {
            if ($page->isDisabled()) {
                return '.';
            }
            if ($page->isCurrent()) {
                return '-';
            }
            return $page->getPage();
        };
        $pages = $pp($p->getFirstPage()) . ' ';
        foreach ($p as $page) {
            $pages .= $pp($page) . ' ';
        }
        $pages .= $pp($p->getLastPage());

        return $pages;
    }
}
