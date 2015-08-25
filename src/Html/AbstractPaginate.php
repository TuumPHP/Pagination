<?php
namespace Tuum\Pagination\Html;

use Tuum\Pagination\Inputs;

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
     * @param array $aria
     */
    public function __construct(array $aria=[])
    {
        $this->aria_label = $aria + $this->aria_label;
    }

    /**
     * @param array $aria
     * @return AbstractPaginate
     */
    public static function forge(array $aria=[])
    {
        $self = new static($aria);
        return $self;
    }

    /**
     * @param int $num
     * @return $this
     */
    public function numLinks($num)
    {
        if (is_numeric($num) && $num > 0) {
            $this->num_links = $num;
        }
        return $this;
    }

    /**
     * @param array $aria
     * @return $this
     */
    public function setAria(array $aria)
    {
        $this->aria_label = array_merge($this->aria_label, $aria);
        return $this;
    }

    /**
     * @API
     * @param Inputs $inputs
     * @return $this
     */
    public function withInputs(Inputs $inputs)
    {
        $self           = clone($this);
        $self->path     = $inputs->path;
        $self->inputs   = $inputs;
        $self->currPage = $inputs->getPage();
        return $self;
    }

    /**
     * @return array
     */
    protected function fillPages()
    {
        $numLinks = $this->num_links;
        $lists = $this->inputs->calcPageList($numLinks);
        $pages = [];
        foreach ($lists as $page) {
            $pages[$page] = $this->constructPage($page);
        }
        return $pages;
    }

    /**
     * @param string|int  $page
     * @param array       $page_list
     * @return bool
     */
    protected function checkIfInPageList($page, array $page_list)
    {
        $pageNum = $this->calcPageNum($page);
        foreach( $page_list as $p) {
            if($p['page'] === $pageNum) return true;
        }
        return false;
    }

    /**
     * @param string|int $page
     * @param string     $label
     * @return array
     */
    protected function constructPage($page, $label = '')
    {
        $pageNum = $this->calcPageNum($page);
        $href = ($pageNum == $this->inputs->getPage()) ?
            '#' : $this->inputs->getPath($pageNum);
        $aria = isset($this->aria_label[$page]) ? $this->aria_label[$page]: '';
        return ['rel' => $page, 'href' => $href, 'aria' => $aria, 'label' => $label, 'page' => $pageNum];
    }

    /**
     * @param $page
     * @return int|string
     */
    protected function calcPageNum($page)
    {
        if (is_numeric($page) ) {
            return $page;
        } elseif (is_string($page)) {
            $method  = 'calc' . ucwords($page) . 'Page';
            $pageNum = $this->inputs->$method();
            return $pageNum;
        }
        throw new \RuntimeException;
    }
}