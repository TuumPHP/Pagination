<?php
namespace Tuum\Pagination\ToHtml;

use Tuum\Pagination\Paginate\Page;
use Tuum\Pagination\Paginate\Paginate;

class ToBootstrap3
{
    /**
     * @var Paginate
     */
    private $paginate;

    /**
     * ToBootstrap3 constructor.
     *
     * @param Paginate $paginate
     */
    public function __construct($paginate)
    {
        $this->paginate = $paginate;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        $paginate = $this->paginate;
        $html     = '<ul class="pagination">';
        $html     .= $this->li($paginate->getFirstPage());
        foreach ($paginate as $page) {
            $html .= $this->li($page);
        }
        $html .= $this->li($paginate->getLastPage());
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param Page $p
     * @return string
     */
    private function li(Page $p)
    {
        if ($p->isDisabled()) {
            return '<li class="disable"><a href="#" >...</a></li>';
        }
        if ($p->isCurrent()) {
            return "<li class=\"active\"><a href=\"#\" >{$p->getPage()}</a></li>\n";
        }

        return "<li><a href=\"{$p->getUrl()}\" >{$p->getPage()}</a></li>\n";
    }
}