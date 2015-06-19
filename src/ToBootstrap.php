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
    private $options = [];

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + [
                'top'       => '&laquo;',
                'prev'      => 'prev',
                'next'      => 'next',
                'last'      => '&raquo;',
                'num_links' => 5,
            ];
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
     * @API
     * @return string
     */
    public function toHtml()
    {
        $numLinks = $this->options['num_links'];
        $html  = '';
        $pages = $this->calculatePagination($numLinks);
        $html .= $this->bootLi('&laquo;', $pages['top_page']);
        $html .= $this->bootLi('prev', $pages['prev_page']);
        foreach ($pages['page'] as $page) {
            $html .= $this->bootLi($page, $page, 'active');
        }
        $html .= $this->bootLi('next', $pages['next_page']);
        $html .= $this->bootLi('&raquo;', $pages['last_page']);
        return "<ul class=\"pagination\">\n{$html}</ul>\n ";
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
     * @param int $numLinks
     * @return array
     */
    private function calculatePagination($numLinks = 5)
    {
        $this->currPage     = $this->inputs->getCurrPage();
        $pages              = [
            'found'     => $this->inputs->getTotal(),
            'curr_page' => $this->currPage,
        ];
        $pages['top_page']  = 1;
        $pages['last_page'] = $lastPage = $this->findLastPage($numLinks);

        // prepare pages
        $pages['page'] = $this->fillPages($numLinks);

        // previous and next pages.
        $pages['prev_page'] = max($this->currPage - 1, 1);
        $pages['next_page'] = min($this->currPage + 1, $lastPage);
        return $pages;
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
            $pages[] = $page;
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
