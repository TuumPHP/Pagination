<?php
namespace WScore\Pagination\Html;

/**
 * Class ToBootstrap3
 *
 * to create pagination html for Bootstrap 3.
 *
 * @package WScore\Pagination\Html
 */
class ToBootstrapMini extends AbstractBootstrap
{
    /**
     * @return array
     */
    public function toArray()
    {
        // list of pages, from $start till $last.
        $page_list = $this->fillPages();

        $pages = [];
        if (!isset($page_list[$this->inputs->calcFirstPage()])) {
            $pages[] = $this->constructPage('first');
        }
        $pages = array_merge($pages, $page_list);
        if (!isset($page_list[$this->inputs->calcLastPage()])) {
            $pages[] = $this->constructPage('last');
        }

        return $this->addAriaLabel($pages);
    }
}
