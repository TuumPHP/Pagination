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
class ToBootstrap3 extends AbstractBootstrap implements ToStringInterface
{
    /**
     * @var array
     */
    protected $options = [
        'top'       => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];

    public $sr_label = [
        'top'  => 'first page',
        'prev' => 'previous page',
        'next' => 'next page',
        'last' => 'last page',
    ];
    
    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }

    /**
     * @param int $numLinks
     * @return array
     */
    protected function calculatePagination($numLinks = 5)
    {
        $lastPage = $this->findLastPage($numLinks);
        $currPage = $this->inputs->getCurrPage();

        $pages   = [];
        $pages[] = ['label' => 'top',  'page' => 1]; // top
        $pages[] = ['label' => 'prev', 'page' => max($currPage - 1, 1)]; // prev

        // list of pages, from $start till $last. 
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = ['label' => 'next', 'page' => min($currPage + 1, $lastPage)]; // next
        $pages[] = ['label' => 'last', 'page' => $lastPage]; // last
        return $pages;
    }

    /**
     * @param array $info
     * @return string
     */
    protected function listItem(array $info)
    {
        $label = isset($info['label']) ? $info['label'] : '';
        $page  = isset($info['page']) ? $info['page'] : '';
        $type  = isset($info['type']) ? $info['type'] : '';
        return $this->bootLi($label, $page, $type);
    }

    /**
     * @param string $label
     * @param int    $page
     * @param string $type
     * @return string
     */
    protected function bootLi($label, $page, $type = 'disable')
    {
        $label = isset($this->options[$label]) ? $this->options[$label] : $label;
        if (isset($this->sr_label[$label])) {
            $srLbl = "aria-label=\"{$this->sr_label[$label]}\"";
        } else {
            $srLbl = '';            
        }
        if ($page != $this->currPage) {
            $key  = $this->inputs->pagerKey;
            $html = "<li><a href='{$this->getRequestUri()}{$key}={$page}' {$srLbl} >{$label}</a></li>\n";
        } elseif ($type == 'disable') {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        } else {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
        }
        return $html;
    }
}
