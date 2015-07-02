<?php
namespace Tuum\Pagination\Factory;

use Tuum\Pagination\Html\Paginate;
use Tuum\Pagination\Html\PaginateInterface;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Html\ToHtmlInterface;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;

class Pagination
{
    protected $aria = [];

    protected $label = [];

    protected $num_links = 3;

    protected $limit = 15;

    /**
     * @var PaginateInterface
     */
    protected $paginate = null;

    /**
     * @var ToHtmlInterface
     */
    protected $toHtml = null;

    /**
     * @return static
     */
    public static function forge()
    {
        return new static();
    }

    /**
     * @param array $aria
     * @return $this
     */
    public function aria(array $aria)
    {
        $this->aria = $aria;
        return $this;
    }

    /**
     * @param array $label
     * @return $this
     */
    public function label(array $label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param int $num
     * @return $this
     */
    public function numLinks($num)
    {
        $this->num_links = $num;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param PaginateInterface $paginate_class
     * @return $this
     */
    public function paginate($paginate_class)
    {
        $this->paginate = $paginate_class;
        return $this;
    }

    /**
     * @param ToHtmlInterface $toHtml_class
     * @return $this
     */
    public function toHtml($toHtml_class)
    {
        $this->toHtml = $toHtml_class;
        return $this;
    }

    /**
     * @return ToHtmlBootstrap
     */
    public function getToHtmlBootstrap()
    {
        if ($this->toHtml) {
            return $this->toHtml;
        }
        $this->toHtml         = new ToHtmlBootstrap();
        $this->toHtml->labels = $this->label + $this->toHtml->labels;
        return $this->toHtml;
    }

    /**
     * @return Paginate
     */
    public function getPaginate()
    {
        if ($this->paginate) {
            return $this->paginate;
        }
        $paginate = new Paginate($this->getToHtmlBootstrap());
        $paginate->aria_label += $this->aria;
        $paginate->num_links = $this->num_links;
        return $paginate;
    }

    /**
     * @param array $option
     * @return Pager
     */
    public function getPager($option = [])
    {
        $inputs = new Inputs($this->getPaginate());
        $pager  = new Pager($option, $inputs);
        return $pager;
    }
}