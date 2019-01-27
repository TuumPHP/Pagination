<?php
namespace Tuum\Pagination;

use Tuum\Pagination\Paginate\Paginate;
use Tuum\Pagination\Paginate\PaginateInterface;
use Tuum\Pagination\ToHtml\ToBootstrap3;

class Inputs
{
    /**
     * @var int
     */
    public $defaultLimit = 20;

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
     * @var string      class name of ToHtmlInterface.
     */
    private $defaultToHtml = ToBootstrap3::class;

    /**
     * Inputs constructor.
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
        $limit = $this->getInt($this->limitKey, 0);
        return $limit > 1 ? $limit : $this->defaultLimit;
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
        $page = $this->getInt($this->pagerKey, 1);
        return $page > 0 ? $page : 1;
    }

    /**
     * @param string $key
     * @param int    $default
     * @return int
     */
    private function getInt($key, $default)
    {
        if (!isset($this->inputs[$key])) {
            return $default;
        }
        if (!$this->inputs[$key]) {
            return $default;
        }
        $value = (int) $this->inputs[$key];
        return is_integer($value) ? $value : $default;
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

    /**
     * @return PaginateInterface
     */
    public function getPagination()
    {
        return Paginate::forge($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $class = $this->defaultToHtml;
        $toHtml = new $class(Paginate::forge($this));
        return (string) $toHtml;
    }

    /**
     * set a class to convert to html pagination.
     * The class must implement ToHtmlInterface.
     *
     * @param string $defaultToHtml
     */
    public function setDefaultToHtml($defaultToHtml)
    {
        $this->defaultToHtml = $defaultToHtml;
    }
}
