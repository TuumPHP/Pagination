<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\Inputs;

abstract class AbstractBootstrap
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
    protected $options = [
        'first'     => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];
    
    /**
     * @var array
     */
    public $aria_label = [
        'first' => 'first page',
        'prev'  => 'previous page',
        'next'  => 'next page',
        'last'  => 'last page',
    ];

    /**
     * @var string
     */
    public $ul_class = 'pagination';
    
    public $default_type = 'disable';


    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }
    
    /**
     * @return array
     */
    abstract public function toArray();
    
    /**
     * @API
     * @param string   $path
     * @param Inputs                 $inputs
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
        $html  = '';
        foreach ($pages as $info) {
            $html .= $this->listItem($info);
        }

        return "<ul class=\"{$this->ul_class}\">\n{$html}</ul>\n";
    }

    /**
     * @param $numLinks
     * @return array
     */
    protected function fillPages($numLinks)
    {
        $start = max($this->currPage - $numLinks, $this->inputs->calcFirstPage());
        $last  = min($this->currPage + $numLinks, $this->inputs->calcLastPage());

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
            $method = 'calc'.ucwords($page).'Page';
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
            $method = 'calc'.ucwords($page).'Page';
            $page = $this->inputs->$method();
        }
        return $this->inputs->getPath($page);
    }

    /**
     * @param string $rel
     * @param string $href
     * @param string $aria
     * @return string
     */
    protected function bootLi($rel, $href, $aria)
    {
        $label = isset($this->options[$rel]) ? $this->options[$rel] : $rel;
        $srLbl = $aria ? " aria-label=\"{$aria}\"" : '';
        if ($href != '#') {
            $html = "<li><a href='{$href}'";
            $html .= $srLbl. " >{$label}</a></li>\n";
        } elseif (is_numeric($rel)) {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
        } else {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        }
        return $html;
    }

    /**
     * @param array $pages
     * @return array
     */
    protected function addAriaLabel(array $pages)
    {
        foreach($pages as $key => $page) {
            $rel = $page['rel'];
            $pages[$key]['aria'] = isset($this->aria_label[$rel]) ? $this->aria_label[$rel] : '';
        }
        return $pages;
    }

    /**
     * @param array $info
     * @return string
     */
    protected function listItem(array $info)
    {
        $label = isset($info['rel']) ? $info['rel'] : '';
        $href  = isset($info['href']) ? $info['href'] : '';
        $aria  = isset($info['aria']) ? $info['aria'] : '';
        return $this->bootLi($label, $href, $aria);
    }

}