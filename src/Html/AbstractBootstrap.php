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
    protected $options = [];

    /**
     * @var array
     */
    protected $sr_label = [];

    /**
     * @var string
     */
    public $ul_class = 'pagination';
    
    public $default_type = 'disable';

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
        $pages = $this->calculatePagination($this->options['num_links']);
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
            $pages[] = ['label' => $page, 'page' => $page, 'type' => 'active'];
        }
        return $pages;
    }

    /**
     * @param int $numLinks
     * @return array
     */
    protected function calculatePagination($numLinks = 5)
    {
        $pages   = [];
        $pages[] = ['label' => 'first', 'page' => $this->inputs->calcFirstPage()]; // top
        $pages[] = ['label' => 'prev',  'page' => $this->inputs->calcPrevPage()]; // prev

        // list of pages, from $start till $last.
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = ['label' => 'next', 'page' => $this->inputs->calcNextPage()]; // next
        $pages[] = ['label' => 'last', 'page' => $this->inputs->calcLastPage()]; // last
        return $pages;
    }

    /**
     * @param string $label
     * @param int    $page
     * @param string $type
     * @return string
     */
    protected function bootLi($label, $page, $type = null)
    {
        $type  = $type ?: $this->default_type;
        $label = isset($this->options[$label]) ? $this->options[$label] : $label;
        if (isset($this->sr_label[$label])) {
            $srLbl = " aria-label=\"{$this->sr_label[$label]}\"";
        } else {
            $srLbl = '';
        }
        if ($page != $this->currPage) {
            $html = "<li><a href='{$this->inputs->getPath($page)}'";
            $html .= $srLbl. " >{$label}</a></li>\n";
        } elseif ($type == 'disable') {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        } elseif ($type == 'none') {
            $html = '';
        } else {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
        }
        return $html;
    }

    /**
     * @param array $info
     * @return string
     */
    protected function listItem(array $info)
    {
        $label = isset($info['label']) ? $info['label'] : '';
        $page  = isset($info['page']) ? $info['page'] : '';
        $type  = isset($info['type']) ? $info['type'] : '';
        return $this->bootLi($label, $page, $type);
    }

}