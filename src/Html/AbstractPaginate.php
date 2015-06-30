<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\Inputs;

abstract class AbstractPaginate implements PaginateInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var Inputs
     */
    protected $inputs;

    /**
     * @var int
     */
    protected $currPage;

    /**
     * @var array
     */
    public $aria_label = [
        'first' => 'first page',
        'prev'  => 'previous page',
        'next'  => 'next page',
        'last'  => 'last page',
    ];

    public $num_links = 5;

    /**
     * @var ToHtmlInterface
     */
    public $toHtml;

    /**
     * @param null|ToHtmlInterface $toHtml
     */
    public function __construct($toHtml = null)
    {
        $this->toHtml = $toHtml ?: new ToHtmlBootstrap();
    }

    /**
     * @return array
     */
    abstract public function toArray();

    /**
     * @API
     * @param string $path
     * @param Inputs $inputs
     * @return $this
     */
    public function withRequestAndInputs($path, $inputs)
    {
        $self           = clone($this);
        $self->path     = $path;
        $self->inputs   = $inputs;
        $self->currPage = $inputs->getPage();
        return $self;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $pages = $this->toArray();
        return $this->toHtml->toString($pages);
    }

    /**
     * @return array
     */
    protected function fillPages()
    {
        $numLinks = $this->num_links;
        $currPage = $this->inputs->getPage();
        $start    = max($currPage - $numLinks, $this->inputs->calcFirstPage());
        $last     = min($currPage + $numLinks, $this->inputs->calcLastPage());

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[$page] = $this->constructPage($page);
        }
        return $pages;
    }

    /**
     * @param string|int $page
     * @return array
     */
    protected function constructPage($page)
    {
        if (!is_numeric($page) && is_string($page)) {
            $method  = 'calc' . ucwords($page) . 'Page';
            $pageNum = $this->inputs->$method();
        } else {
            $pageNum = $page;
        }
        $href = ($pageNum == $this->inputs->getPage()) ?
            '#' : $this->inputs->getPath($pageNum);
        return ['rel' => $page, 'href' => $href];
    }

    /**
     * @param string|int $page
     * @return string
     */
    protected function href($page)
    {
        if (!is_numeric($page) && is_string($page)) {
            $method = 'calc' . ucwords($page) . 'Page';
            $page   = $this->inputs->$method();
        }
        return $this->inputs->getPath($page);
    }

    /**
     * @param array $pages
     * @return array
     */
    protected function addAriaLabel(array $pages)
    {
        foreach ($pages as $key => $page) {
            $rel                 = $page['rel'];
            $pages[$key]['aria'] = isset($this->aria_label[$rel]) ? $this->aria_label[$rel] : '';
        }
        return $pages;
    }

}