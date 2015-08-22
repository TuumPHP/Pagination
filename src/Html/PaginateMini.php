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
        $pages[] = $this->constructPage('prev', '&laquo;');
        if (!$this->checkIfInPageList('first', $page_list)) {
            $pages[] = $this->constructPage('first', 1);
            if (!$this->checkIfInPageList(2, $page_list)) {
                $pages[] = [];
            }
        }
        $pages = array_merge($pages, $page_list);
        if (!$this->checkIfInPageList('last', $page_list)) {
            if (!$this->checkIfInPageList($this->calcPageNum('last')-1, $page_list)) {
                $pages[] = [];
            }
            $pages[] = $this->constructPage('last', $this->calcPageNum('last'));
        }
        $pages[] = $this->constructPage('next', '&raquo;');

        return $pages;
    }
}
