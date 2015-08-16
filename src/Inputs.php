<?php
namespace Tuum\Pagination;

class Inputs
{
    /**
     * @var string
     */
    public $pagerKey = '_page';

    /**
     * @var string
     */
    public $limitKey = '_limit';

    /**
     * @var int|null
     */
    private $total = null;

    /**
     * @var array
     */
    private $list = [];

    /**
     * @var string
     */
    public $path = '';

    /**
     * @var array
     */
    public $inputs = [];

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * get the limit, i.e. number of data per page.
     *
     * @return int
     */
    public function getLimit()
    {
        return (int)isset($this->inputs[$this->limitKey]) ? $this->inputs[$this->limitKey] : 20;
    }

    /**
     * get the offset for retrieving data.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->getLimit() * ($this->getPage() - 1);
    }

    /**
     * get the current page number, starting from 1.
     *
     * @return int
     */
    public function getPage()
    {
        return (int)isset($this->inputs[$this->pagerKey]) ? $this->inputs[$this->pagerKey] : 1;
    }

    /**
     * get any key from query.
     *
     * @param string     $key
     * @param null|mixed $alt
     * @return null|mixed
     */
    public function get($key, $alt = null)
    {
        return array_key_exists($key, $this->inputs)
            ? $this->inputs[$key]
            : $this->inputs[$key] = $alt;
    }

    /**
     * get total number of data.
     * - total: number of all the possible data which can be retrieved.
     * - count: number of data in the current list.
     *
     * @return int|null
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * set the total of data.
     *
     * @param int|null $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * set the data for list.
     *
     * @param mixed $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }

    /**
     * get the data for list.
     *
     * @return null|array|mixed
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * get the count, i.e. number of data in the current list.
     * count is the number of data in the current list.
     *
     * @return int
     */
    public function getCount()
    {
        if (isset($this->list) && is_array($this->list)) {
            return count($this->list);
        }
        return 0;
    }

    /**
     * same as getPage() method.
     *
     * @return int
     */
    public function calcSelfPage()
    {
        return $this->getPage();
    }

    /**
     * calculates the first page number, that is 1.
     *
     * @return int
     */
    public function calcFirstPage()
    {
        return 1;
    }

    /**
     * calculates the last pager number.
     *
     * @return int
     */
    public function calcLastPage()
    {
        $total = $this->getTotal();
        if (!$total) {
            return $this->getPage() + 1;
        }
        $pages = $this->getLimit();
        return (integer)(ceil($total / $pages));
    }

    /**
     * calculates the next page number.
     *
     * @return int
     */
    public function calcNextPage()
    {
        return min($this->getPage() + 1, $this->calcLastPage());
    }

    /**
     * check if the next page exists (i.e. current page is not the last page).
     *
     * @return bool
     */
    public function existsNextPage()
    {
        return $this->getPage() < $this->calcLastPage();
    }

    /**
     * calculates the previous page number.
     *
     * @return int
     */
    public function calcPrevPage()
    {
        return max($this->getPage() - 1, $this->calcFirstPage());
    }

    /**
     * @return bool
     */
    public function existsPrevPage()
    {
        return $this->getPage() > $this->calcFirstPage();
    }

    /**
     * @param int $numLinks
     * @return array
     */
    public function calcPageList($numLinks)
    {
        $currPage = $this->getPage();
        $lastPage = $this->calcLastPage();

        $extra_1  = max(0, $numLinks - $currPage);
        $extra_2  = max(0, $numLinks - ($lastPage - $currPage));
        if ($extra_1 > 0 || $currPage === $numLinks) {
            $extra_2 += $extra_1 + 1;
        }
        if ($extra_2 > 0) {
            $extra_1 += $extra_2;
        }
        $start    = max($currPage - $numLinks - $extra_1, $this->calcFirstPage());
        $last     = min($currPage + $numLinks + $extra_2, $this->calcLastPage());

        $pages = [];
        for ($p = $start; $p <= $last; $p++) {
            $pages[] = $p;
        }
        return $pages;
    }

    /**
     * @param null|int $page
     * @return string
     */
    public function getPath($page = null)
    {
        if (is_null($page)) {
            return $this->path;
        }
        $page = (int)$page;
        return $this->path . '?' . $this->pagerKey . '=' . $page;
    }
}