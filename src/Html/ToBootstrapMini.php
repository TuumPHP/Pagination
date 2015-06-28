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
class ToBootstrapMini extends AbstractBootstrap implements ToStringInterface
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
        $page_list = $this->fillPages($numLinks);

        $pages[] = [];
        if (!isset($page_list[$this->inputs->calcFirstPage()])) {
            $pages[] = ['label' => 'first', 'page' => $this->inputs->calcFirstPage()]; // top
        }
        $pages = array_merge($pages, $page_list);
        if (!isset($page_list[$this->inputs->calcLastPage()])) {
            $pages[] = ['label' => 'last', 'page' => $this->inputs->calcLastPage()]; // top
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
