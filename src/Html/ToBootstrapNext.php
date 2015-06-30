<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\ToStringInterface;

/**
 * Class ToBootstrap3
 *
 * to create pagination html for Bootstrap 3.
 *
 * @package WScore\Pagination\Html
 */
class ToBootstrapNext extends AbstractBootstrap implements ToStringInterface
{
    /**
     * @return array
     */
    public function toArray()
    {
        $numLinks = $this->options['num_links'];

        // list of pages, from $start till $last.
        $page_list = $this->fillUpToPages($numLinks);

        $pages = [];
        if (!isset($page_list[$this->inputs->calcFirstPage()])) {
            $pages[] = $this->constructPage('first');
        }
        $pages = array_merge($pages, $page_list);
        if (!isset($page_list[$this->inputs->calcNextPage()])) {
            $pages[] = $this->constructPage('next');
        }

        return $this->addAriaLabel($pages);
    }
    
    protected function fillUpToPages($numLinks)
    {
        $start = max($this->inputs->calcSelfPage() - $numLinks, $this->inputs->calcFirstPage());
        $last  = $this->inputs->calcSelfPage();

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[$page] = $this->constructPage($page);
        }
        return $pages;
    }
    
    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }
}
