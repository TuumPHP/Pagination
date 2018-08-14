<?php
namespace Tuum\Pagination\ToHtml;

use Tuum\Pagination\Paginate\Page;
use Tuum\Pagination\Paginate\Paginate;
use Tuum\Pagination\Paginate\PaginateInterface;

class ToBootstrap4 implements ToHtmlInterface
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
        $html     = '<nav aria-label="CPD point list"><ul class="pagination">';
        $html     .= $this->liLabel($paginate->getPrevPage(), '&lt;');
        $html     .= $this->li($paginate->getFirstPage());
        foreach ($paginate as $page) {
            $html .= $this->li($page);
        }
        $html .= $this->li($paginate->getLastPage());
        $html     .= $this->liLabel($paginate->getNextPage(), '&gt;');
        $html .= '</ul></nav>';

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
        $only  = $p->isCurrent('<span class=\'page-link sr-only\'>current</span>', '');
        if ($p->isDisabled()) {
            return "<li class=\"page-item disabled\"><a class='page-link' href=\"{$href}\" >{$label}</a></li>";
        }
        if ($p->isCurrent()) {
            return "<li class=\"page-item active\"><a class='page-link' href=\"{$href}\" >{$label}{$only}</a></li>\n";
        }

        return "<li class='page-item'><a class='page-link' href=\"{$href}\" >{$label}{$only}</a></li>\n";
    }
    
    private function liLabel(Page $p, $label)
    {
        return "<li class=\"{$p->isCurrent('disabled')}\"><a class='page-link' href=\"{$p->isCurrent('#', $p->getUrl())}\" >{$label}</a></li>\n";
    }
}