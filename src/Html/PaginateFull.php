<?php
namespace Tuum\Pagination\Html;

class PaginateFull extends AbstractPaginate
{
    /**
     * @return array
     */
    public function toArray()
    {
        $pages   = [];
        $pages[] = $this->constructPage('first');
        $pages[] = $this->constructPage('prev');

        // list of pages, from $start till $last.
        $pages = array_merge($pages, $this->fillPages());

        $pages[] = $this->constructPage('next');
        $pages[] = $this->constructPage('last');

        return $pages;
    }
}
