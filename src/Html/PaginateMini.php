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
        if (!$this->checkIfInPageList('first', $page_list)) {
            $pages[] = $this->constructPage('first');
        }
        $pages = array_merge($pages, $page_list);
        if (!$this->checkIfInPageList('last', $page_list)) {
            $pages[] = $this->constructPage('last');
        }

        return $pages;
    }
}
