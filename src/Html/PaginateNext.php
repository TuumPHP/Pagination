<?php
namespace Tuum\Pagination\Html;

/**
 * Class ToBootstrap3
 *
 * to create pagination html for Bootstrap 3.
 *
 * @package WScore\Pagination\Html
 */
class PaginateNext extends AbstractPaginate
{
    /**
     * @return array
     */
    public function toArray()
    {
        // list of pages, from $start till $last.
        $page_list = $this->fillUpToPages();

        $pages = [];
        if (!$this->checkIfInPageList('first', $page_list)) {
            $pages[] = $this->constructPage('first');
        }
        $pages = array_merge($pages, $page_list);
        if (!$this->checkIfInPageList('next', $page_list)) {
            $pages[] = $this->constructPage('next');
        }

        return $pages;
    }

    protected function fillUpToPages()
    {
        $numLinks = $this->num_links;
        $start    = max($this->inputs->calcSelfPage() - $numLinks, $this->inputs->calcFirstPage());
        $last     = $this->inputs->calcSelfPage();

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[$page] = $this->constructPage($page);
        }
        return $pages;
    }
}
