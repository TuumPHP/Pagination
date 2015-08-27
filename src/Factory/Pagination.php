<?php
namespace Tuum\Pagination\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Pagination\Html\PaginateMini;
use Tuum\Pagination\Html\PaginateInterface;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Html\ToHtmlInterface;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;

class Pagination
{
    /**
     * for aria-labels used in PaginateInterface objects.
     *
     * @var array
     */
    public $aria = [];

    /**
     * for labeling page link used in ToHtmlInterface objects.
     *
     * @var array
     */
    public $label = [];

    /**
     * number of links used in PaginateInterface objects.
     *
     * @var int
     */
    public $num_links = 0;

    /**
     * @var PaginateInterface
     */
    public $paginate = null;

    /**
     * @var ToHtmlInterface
     */
    public $toHtml = null;

    /**
     * @var Inputs
     */
    public $inputs;

    /**
     * @var Pager
     */
    public $pager;

    /**
     * @param Pager             $pager
     * @param PaginateInterface $paginate
     * @param ToHtmlInterface   $toHtml
     */
    public function __construct(
        Pager $pager,
        PaginateInterface $paginate,
        ToHtmlInterface $toHtml
    ) {
        $this->pager    = $pager;
        $this->paginate = $paginate;
        $this->toHtml   = $toHtml;
    }

    /**
     * @param Pager                  $pager
     * @param PaginateInterface|null $paginate
     * @param ToHtmlInterface|null   $toHtml
     * @return static
     */
    public static function forge(
        $pager = null,
        $paginate = null,
        $toHtml = null
    ) {
        $objects = func_get_args();
        foreach ($objects as $obj) {
            if (!is_object($obj)) {
                continue;
            } elseif ($obj instanceof PaginateInterface) {
                $paginate = $obj;
            } elseif ($obj instanceof ToHtmlInterface) {
                $toHtml = $obj;
            } elseif ($obj instanceof Pager) {
                $pager = $obj;
            }
        }
        $pager    = $pager ?: new Pager();
        $paginate = $paginate ?: PaginateMini::forge();
        $toHtml   = $toHtml ?: ToHtmlBootstrap::forge();
        return new static($pager, $paginate, $toHtml);
    }

    /**
     * calls Pager using PSR-7 request.
     * this is an immutable call; must accept the returning object.
     * i.e. $pager = $pager->call($req, ...);
     *
     * @param ServerRequestInterface $request
     * @param \Closure               $callback
     * @return static
     */
    public function call($request, $callback)
    {
        $self         = clone($this);
        $self->inputs = $self->pager->withRequest($request)->call($callback);
        return $self;
    }

    /**
     * calls Pager using query ($_GET) and path.
     * immutable call.
     *
     * @param array    $query
     * @param string   $path
     * @param \Closure $callback
     * @return static
     */
    public function callQuery($query, $path, $callback)
    {
        $self         = clone($this);
        $self->inputs = $self->pager->withQuery($query, $path)->call($callback);
        return $self;
    }

    /**
     * gets the array of pages.
     *
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
        $paginate = $this->paginate->withInputs($this->inputs);
        $paginate->setAria($this->aria);
        $paginate->numLinks($this->num_links);
        return $paginate;
    }

    /**
     * generates pagination HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->toHtml
            ->withPaginate($this->getPaginate())
            ->setLabels($this->label)
            ->toString();
    }

    /**
     * alias to toHtml method.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }

    /**
     * get a total number of queries as set by setTotal.
     *
     * @return int|null
     */
    public function getTotal()
    {
        return $this->inputs->getTotal();
    }

    /**
     * get the limit (per-page count).
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->inputs->getLimit();
    }

    /**
     * get the count of the found list.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->inputs->getCount();
    }

    /**
     * get the found result.
     *
     * @return int
     */
    public function getList()
    {
        return $this->inputs->getList();
    }

    /**
     * get the current page number.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->inputs->getPage();
    }
}