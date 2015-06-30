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
        $pages[] = $this->constructPage('first');
        $pages[] = $this->constructPage('prev');

        // list of pages, from $start till $last.
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = $this->constructPage('next');
        $pages[] = $this->constructPage('last');

        return $this->addAriaLabel($pages);
    }
}
