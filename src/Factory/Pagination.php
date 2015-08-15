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
    /**
     * for aria-labels used in PaginateInterface objects.
     * @var array
     */
    public $aria = [];

    /**
     * for labeling page link used in ToHtmlInterface objects.
     * @var array
     */
    public $label = [];

    /**
     * number of links used in PaginateInterface objects.
     * @var int
     */
    public $num_links = 3;

    /**
     * @var Pager
     */
    protected $pager;

    /**
     * @var PaginateInterface
     */
    protected $paginate = null;

    /**
     * @var ToHtmlInterface
     */
    protected $toHtml = null;

    /**
     * @var Inputs
     */
    protected $inputs;

    /**
     * @param PaginateInterface $paginate
     * @param ToHtmlInterface   $toHtml
     */
    public function __construct(
        PaginateInterface $paginate = null,
        ToHtmlInterface $toHtml = null
    ) {
        $this->paginate = $paginate ?: Paginate::forge();
        $this->toHtml   = ToHtmlBootstrap::forge();
    }

    /**
     * @param PaginateInterface|null $paginate
     * @param ToHtmlInterface|null   $toHtml
     * @return static
     */
    public static function forge(
        PaginateInterface $paginate = null,
        ToHtmlInterface $toHtml = null
    ) {
        return new static($paginate, $toHtml);
    }

    /**
     * @param Pager $pager
     * @return Pagination
     */
    public function withPager(Pager $pager)
    {
        $self        = clone($this);
        $self->pager = $pager;
        return $self;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function call($callback)
    {
        $this->inputs = $this->pager->call($callback);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getPaginate()->toArray();
    }

    /**
     * @return PaginateInterface
     */
    private function getPaginate()
    {
        $this->paginate->setAria($this->aria);
        $this->paginate->numLinks($this->num_links);
        return $this->paginate->withInputs($this->inputs);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtml->setLabels($this->label);
        return $this->toHtml->withPaginate($this->getPaginate())->toString();
    }
}