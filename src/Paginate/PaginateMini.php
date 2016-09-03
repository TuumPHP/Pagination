<?php
namespace Tuum\Pagination\Paginate;

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
        if ($this->num_links < 2) {
            throw new \InvalidArgumentException('number of links must be larger than 2.');
        }
        // list of pages, from $start till $last.
        $page_list = $this->fillPages();

        $pages = [];
        $pages[] = $this->constructPage('prev', '<');
        if (!$this->checkIfInPageList('first', $page_list)) {
            $page_list = array_slice($page_list, 2);
            $pages[] = $this->constructPage('first', 1);
            $pages[] = [];
        }
        $pages = array_merge($pages, $page_list);
        if (!$this->checkIfInPageList('last', $page_list)) {
            $pages = array_slice($pages, 0, -2);
            $pages[] = [];
            $pages[] = $this->constructPage('last', $this->calcPageNum('last'));
        }
        $pages[] = $this->constructPage('next', '>');

        return $pages;
    }
}
