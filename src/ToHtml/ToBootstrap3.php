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
    public function __construct($paginate = null)
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
        $last = $paginate->getLastPage();
        if ($last->getPage() > 1) {
            $html .= $this->li($last);
        }
        $html .= $this->liLabel($paginate->getNextPage(), '&gt;');
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param Page $p
     * @return string
     */
    private function li(Page $p)
    {
        $label = $p->isDisabled('...', $p->getPage());
        $href  = $p->isCurrent('#', $p->getUrl());
        $only  = $p->isCurrent('<span class=\'sr-only\'>current</span>', '');
        if ($p->isDisabled()) {
            return "<li class=\"disabled\"><a href=\"{$href}\" >{$label}</a></li>";
        }
        if ($p->isCurrent()) {
            return "<li class=\"active\"><a href=\"{$href}\" >{$label}{$only}</a></li>\n";
        }

        return "<li><a href=\"{$href}\" >{$label}{$only}</a></li>\n";
    }
    
    private function liLabel(Page $p, $label)
    {
        return "<li class=\"{$p->isCurrent('disabled')}\"><a href=\"{$p->isCurrent('#', $p->getUrl())}\" >{$label}</a></li>\n";
    }
}