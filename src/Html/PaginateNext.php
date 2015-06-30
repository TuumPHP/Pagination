<?php
namespace WScore\Pagination\Html;

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
        $pages = $this->constructPageIfNotInPages('first', $pages, $page_list);
        $pages = array_merge($pages, $page_list);
        $pages = $this->constructPageIfNotInPages('next', $pages, $page_list);

        return $this->addAriaLabel($pages);
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
