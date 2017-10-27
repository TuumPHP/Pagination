<?php
namespace Tuum\Pagination\ToHtml;

use Tuum\Pagination\Paginate\Page;
use Tuum\Pagination\Paginate\Paginate;
use Tuum\Pagination\Paginate\PaginateInterface;

class ToBootstrap3 implements ToHtmlInterface
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
     * @param PaginateInterface $paginate
     * @return ToHtmlInterface
     */
    public function setPaginate(PaginateInterface $paginate)
    {
        $this->paginate = $paginate;
        return $this;
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
        $html     .= $this->liLabel($paginate->getPrevPage(), '&lt;');
        $html     .= $this->li($paginate->getFirstPage());
        foreach ($paginate as $page) {
            $html .= $this->li($page);
        }
        $html .= $this->li($paginate->getLastPage());
        $html     .= $this->liLabel($paginate->getNextPage(), '&gt;');
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param Page $p
     * @return string
     */
    private function li(Page $p, $label = null)
    {
        $label = $label ?: $p->getPage();
        if ($p->isDisabled()) {
            return '<li class="disabled"><a href="#" >...</a></li>';
        }
        if ($p->isCurrent()) {
            return "<li class=\"active\"><a href=\"#\" >{$label}<span class='sr-only'>current</span></a></li>\n";
        }

        return "<li><a href=\"{$p->getUrl()}\" >{$label}</a></li>\n";
    }
    
    private function liLabel(Page $p, $label)
    {
        if ($p->isCurrent()) {
            return "<li class=\"disabled\"><a href=\"#\" >{$label}</a></li>\n";
        }

        return "<li><a href=\"{$p->getUrl()}\" >{$label}</a></li>\n";
    }
}