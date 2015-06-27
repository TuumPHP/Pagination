<?php
namespace WScore\Pagination;

class Inputs implements \ArrayAccess
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

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $offset = $this->getOffsetKey($offset);

        return array_key_exists($offset, $this->inputs);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $offset = $this->getOffsetKey($offset);
        
        return array_key_exists($offset, $this->inputs) ? $this->inputs[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $offset = $this->getOffsetKey($offset);
        $this->inputs[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        $offset = $this->getOffsetKey($offset);

        if (array_key_exists($offset, $this->inputs)) {
            unset ($this->inputs[$offset]);
        }
    }

    /**
     * @param $offset
     * @return string
     */
    private function getOffsetKey($offset)
    {
        $offset = '_' . ltrim($offset, '_');

        return $offset;
    }
}