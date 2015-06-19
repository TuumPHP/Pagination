<?php
namespace WScore\Pagination;

use Psr\Http\Message\ServerRequestInterface;

class ToBootstrap implements ToHtmlInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var Inputs
     */
    private $inputs;

    /**
     * @var int
     */
    private $currPage;

    /**
     * @var array
     */
    private $options = [
        'top'       => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }

    /**
     * @API
     * @param ServerRequestInterface $request
     * @param Inputs                 $inputs
     * @return $this
     */
    public function withRequestAndInputs($request, $inputs)
    {
        $self           = clone($this);
        $self->request  = $request;
        $self->inputs   = $inputs;
        $self->currPage = $inputs->getCurrPage();
        return $self;
    }

    /**
     * @return string
     */
    private function getRequestUri()
    {
        return $this->request->getUri()->getPath() . '?';
    }

    // +----------------------------------------------------------------------+
    //  preparing for pagination list. Yep, this should go any other class.
    // +----------------------------------------------------------------------+
    /**
     * @return string
     */
    public function __toString()
    {
        $pages = $this->calculatePagination($this->options['num_links']);
        $html = '';
        foreach($pages as $info) {
            $html .= $this->listItem($info);
        }
        return "<ul class=\"pagination\">\n{$html}</ul>\n";
    }

    /**
     * @param int $numLinks
     * @return array
     */
    private function calculatePagination($numLinks = 5)
    {
        $lastPage = $this->findLastPage($numLinks);
        $currPage = $this->inputs->getCurrPage();

        $pages   = [];
        $pages[] = ['label' => 'top', 'page' => 1]; // top
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
    private function listItem(array $info)
    {
        $label = isset($this->options[$info['label']]) ? $this->options[$info['label']] : $info['label'];
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
    private function bootLi($label, $page, $type = 'disable')
    {
        if ($page != $this->currPage) {
            $key  = $this->inputs->pagerKey;
            $html = "<li><a href='{$this->getRequestUri()}{$key}={$page}' >{$label}</a></li>\n";
        } elseif ($type == 'disable') {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        } else {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
        }
        return $html;
    }

    /**
     * @param $numLinks
     * @return array
     */
    private function fillPages($numLinks)
    {
        $start = max($this->currPage - $numLinks, 1);
        $last  = min($this->currPage + $numLinks, $this->findLastPage($numLinks));

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[] = ['label' => $page, 'page' => $page, 'type' => 'active'];
        }
        return $pages;
    }

    /**
     * @param int $numLinks
     * @return int
     */
    private function findLastPage($numLinks)
    {
        // total and perPage is set.
        $total = $this->inputs->getTotal();
        $pages = $this->inputs->getLimit();
        if (!is_null($total) && $pages) {
            return (integer)(ceil($total / $pages));
        }
        return $this->currPage + $numLinks;
    }
}
