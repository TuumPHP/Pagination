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
    protected $aria_label = [];

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
            $pages[$page] = ['rel' => $page, 'page' => $page, 'type' => 'active'];
        }
        return $pages;
    }

    /**
     * @param string $rel
     * @param int    $page
     * @param string $type
     * @param string $aria
     * @return string
     */
    protected function bootLi($rel, $page, $type, $aria)
    {
        $type  = $type ?: $this->default_type;
        $label = isset($this->options[$rel]) ? $this->options[$rel] : $rel;
        $srLbl = $aria ? " aria-label=\"{$aria}\"" : '';
        if ($page != $this->currPage) {
            $html = "<li><a href='{$this->inputs->getPath($page)}'";
            $html .= $srLbl. " >{$label}</a></li>\n";
        } elseif ($type == 'disable') {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        } else {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
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
        $page  = isset($info['page']) ? $info['page'] : '';
        $type  = isset($info['type']) ? $info['type'] : '';
        $aria  = isset($info['aria']) ? $info['aria'] : '';
        return $this->bootLi($label, $page, $type, $aria);
    }

}