<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\ToStringInterface;

class ToBootstrap extends AbstractBootstrap implements ToStringInterface
{
    /**
     * @var array
     */
    protected $options = [
        'first'     => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];

    /**
     * @param int $numLinks
     * @return array
     */
    protected function calculatePagination($numLinks = 5)
    {
        $pages   = [];
        $pages[] = ['label' => 'first', 'page' => $this->inputs->calcFirstPage()]; // top
        $pages[] = ['label' => 'prev',  'page' => $this->inputs->calcPrevPage()]; // prev

        // list of pages, from $start till $last.
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = ['label' => 'next', 'page' => $this->inputs->calcNextPage()]; // next
        $pages[] = ['label' => 'last', 'page' => $this->inputs->calcLastPage()]; // last
        return $pages;
    }

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }
}
