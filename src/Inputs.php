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
     * @return int
     */
    public function getLimit()
    {
        return (int)isset($this->inputs[$this->limitKey]) ? $this->inputs[$this->limitKey] : 20;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->getLimit() * ($this->getCurrPage() - 1);
    }

    /**
     * @return int
     */
    public function getCurrPage()
    {
        return (int)isset($this->inputs[$this->pagerKey]) ? $this->inputs[$this->pagerKey] : 1;
    }

    /**
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
     * @return int|null
     */
    public function getTotal()
    {
        return array_key_exists($this->totalKey, $this->inputs) ? (int) $this->inputs[$this->totalKey]: null;
    }

    /**
     * @param int|null $total
     */
    public function setTotal($total)
    {
        $this->inputs[$this->totalKey] = $total;
    }

    /**
     * @param mixed $list
     */
    public function setList($list)
    {
        $this->inputs[$this->listKey] = $list;
    }

    /**
     * @return null|mixed
     */
    public function getList()
    {
        return array_key_exists($this->listKey, $this->inputs) ? $this->inputs[$this->listKey] : null;
    }
}