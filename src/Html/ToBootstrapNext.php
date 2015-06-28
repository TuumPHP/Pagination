<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\ToStringInterface;

/**
 * Class ToBootstrap3
 *
 * to create pagination html for Bootstrap 3.
 *
 * @package WScore\Pagination\Html
 */
class ToBootstrapNext extends AbstractBootstrap implements ToStringInterface
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

    public $sr_label = [
        'first' => 'first page',
        'prev'  => 'previous page',
        'next'  => 'next page',
        'last'  => 'last page',
    ];

    /**
     * @param int $numLinks
     * @return array
     */
    protected function calculatePagination($numLinks = 5)
    {
        // list of pages, from $start till $last.
        $page_list = $this->fillUpToPages($numLinks);

        $pages = [];
        if (!isset($page_list[$this->inputs->calcFirstPage()])) {
            $pages[] = ['label' => 'first', 'page' => $this->inputs->calcFirstPage()]; // top
        }
        $pages = array_merge($pages, $page_list);
        if (!isset($page_list[$this->inputs->calcNextPage()])) {
            $pages[] = ['label' => 'next', 'page' => $this->inputs->calcNextPage()]; // top
        }

        return $pages;
    }
    
    protected function fillUpToPages($numLinks)
    {
        $start = max($this->inputs->calcSelfPage() - $numLinks, $this->inputs->calcFirstPage());
        $last  = $this->inputs->calcSelfPage();

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[$page] = ['label' => $page, 'page' => $page, 'type' => 'active'];
        }
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
