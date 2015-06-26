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
        $self->currPage = $inputs->getCurrPage();
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
     * @return string
     */
    protected function getRequestUri()
    {
        return $this->path . '?';
    }

    /**
     * @param $numLinks
     * @return array
     */
    protected function fillPages($numLinks)
    {
        $start = max($this->currPage - $numLinks, 1);
        $last  = min($this->currPage + $numLinks, $this->findLastPage($numLinks));

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[] = ['label' => $page, 'page' => $page, 'type' => 'active'];
        }
        return $pages;
    }

    /**
     * @param int $numLinks
     * @return int
     */
    protected function findLastPage($numLinks)
    {
        // total and perPage is set.
        $total = $this->inputs->getTotal();
        $pages = $this->inputs->getLimit();
        if (!is_null($total) && $pages) {
            return (integer)(ceil($total / $pages));
        }
        return $this->currPage + $numLinks;
    }

    /**
     * @param int $numLinks
     * @return array
     */
    protected function calculatePagination($numLinks = 5)
    {
        $lastPage = $this->findLastPage($numLinks);
        $currPage = $this->inputs->getCurrPage();

        $pages   = [];
        $pages[] = ['label' => 'first', 'page' => 1]; // top
        $pages[] = ['label' => 'prev',  'page' => max($currPage - 1, 1)]; // prev

        // list of pages, from $start till $last.
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = ['label' => 'next', 'page' => min($currPage + 1, $lastPage)]; // next
        $pages[] = ['label' => 'last', 'page' => $lastPage]; // last
        return $pages;
    }

    /**
     * @param string $label
     * @param int    $page
     * @param string $type
     * @return string
     */
    protected function bootLi($label, $page, $type = 'disable')
    {
        $label = isset($this->options[$label]) ? $this->options[$label] : $label;
        if (isset($this->sr_label[$label])) {
            $srLbl = " aria-label=\"{$this->sr_label[$label]}\"";
        } else {
            $srLbl = '';
        }
        if ($page != $this->currPage) {
            $key  = $this->inputs->pagerKey;
            $html = "<li><a href='{$this->getRequestUri()}{$key}={$page}'";
            $html .= $srLbl. " >{$label}</a></li>\n";
        } elseif ($type == 'disable') {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
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