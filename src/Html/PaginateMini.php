<?php
namespace Tuum\Pagination\Html;

/**
 * Class ToBootstrap3
 *
 * to create pagination html for Bootstrap 3.
 *
 * @package WScore\Pagination\Html
 */
class PaginateMini extends AbstractPaginate
{
    /**
     * @return array
     */
    public function toArray()
    {
        // list of pages, from $start till $last.
        $page_list = $this->fillPages();

        $pages = [];
        $pages = $this->constructPageIfNotInPages('first', $pages, $page_list);
        $pages = array_merge($pages, $page_list);
        $pages = $this->constructPageIfNotInPages('last', $pages, $page_list);

        return $pages;
    }
}
