<?php
namespace WScore\Pagination;

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
     * @var string
     */
    public $totalKey = '_total';

    /**
     * @var string
     */
    public $listKey = '_list';

    /**
     * @var string
     */
    public $path = '';

    /**
     * @var array
     */
    private $inputs = [];

    /**
     * @param array $inputs
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
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
        return array_key_exists($this->totalKey, $this->inputs) ? (int) $this->inputs[$this->totalKey]: null;
    }

    /**
     * set the total of data.
     * 
     * @param int|null $total
     */
    public function setTotal($total)
    {
        $this->inputs[$this->totalKey] = $total;
    }

    /**
     * set the data for list. 
     * 
     * @param mixed $list
     */
    public function setList($list)
    {
        $this->inputs[$this->listKey] = $list;
    }

    /**
     * get the data for list.
     * 
     * @return null|mixed
     */
    public function getList()
    {
        return array_key_exists($this->listKey, $this->inputs) ? $this->inputs[$this->listKey] : null;
    }

    /**
     * get the count, i.e. number of data in the current list. 
     * count is the number of data in the current list. 
     * 
     * @return int
     */
    public function getCount()
    {
        if (array_key_exists($this->listKey, $this->inputs) && is_array($this->inputs[$this->listKey])) {
            return count($this->inputs[$this->listKey]);
        }
        return 0;
    }
}