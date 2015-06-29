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
     * @return array
     */
    public function toArray()
    {
        $numLinks = $this->options['num_links'];

        // list of pages, from $start till $last.
        $page_list = $this->fillPages($numLinks);

        $pages = [];
        if (!isset($page_list[$this->inputs->calcFirstPage()])) {
            $pages[] = ['rel' => 'first', 'page' => $this->inputs->calcFirstPage()]; // top
        }
        $pages = array_merge($pages, $page_list);
        if (!isset($page_list[$this->inputs->calcLastPage()])) {
            $pages[] = ['rel' => 'last', 'page' => $this->inputs->calcLastPage()]; // top
        }

        return $this->addAriaLabel($pages);
    }
    
    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }
}
