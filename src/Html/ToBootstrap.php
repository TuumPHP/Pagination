<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\ToStringInterface;

class ToBootstrap extends AbstractBootstrap implements ToStringInterface
{
    /**
     * @return array
     */
    public function toArray()
    {
        $numLinks = $this->options['num_links'];

        $pages   = [];
        $pages[] = ['rel' => 'first', 'page' => $this->inputs->calcFirstPage()]; // top
        $pages[] = ['rel' => 'prev',  'page' => $this->inputs->calcPrevPage()]; // prev

        // list of pages, from $start till $last.
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = ['rel' => 'next', 'page' => $this->inputs->calcNextPage()]; // next
        $pages[] = ['rel' => 'last', 'page' => $this->inputs->calcLastPage()]; // last
        
        return $this->addAriaLabel($pages);
    }
}
