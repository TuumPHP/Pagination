<?php
namespace Tuum\Pagination\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Pagination\Html\Paginate;
use Tuum\Pagination\Html\PaginateInterface;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Html\ToHtmlInterface;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;

class Pagination
{
    public static $aria = [];

    public static $label = [];

    public static $num_links = 3;

    public $limit = 15;

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
    private $inputs;

    /**
     * @param Pager             $pager
     * @param PaginateInterface $paginate
     * @param ToHtmlInterface   $toHtml
     */
    public function __construct(Pager $pager, PaginateInterface $paginate, ToHtmlInterface $toHtml)
    {
        $this->pager = $pager;
        $this->paginate = $paginate;
        $this->toHtml = $toHtml;
    }

    /**
     * @param Pager                  $pager
     * @param PaginateInterface|null $paginate
     * @param ToHtmlInterface|null   $toHtml
     * @return static
     */
    public static function forge(
        Pager $pager,
        PaginateInterface $paginate = null,
        ToHtmlInterface $toHtml = null)
    {
        $paginate = $paginate ?: Paginate::forge(static::$aria);
        $paginate->numLinks(static::$num_links);
        $toHtml = $toHtml ?: ToHtmlBootstrap::forge(static::$label);
        return new static($pager, $paginate, $toHtml);
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
        return $this->paginate->withInputs($this->inputs);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return $this->toHtml->withPaginate($this->getPaginate())->toString();
    }
}