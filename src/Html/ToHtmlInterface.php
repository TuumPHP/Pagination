<?php
namespace Tuum\Pagination\Html;

/**
 * Interface ToHtml
 *
 * @package WScore\Pagination\Html
 */
interface ToHtmlInterface
{
    /**
     * construct HTML (i.e. string) from PaginateInterface::toArray.
     *
     * @param PaginateInterface $paginate
     * @return string
     */
    public function toString(PaginateInterface $paginate);
}