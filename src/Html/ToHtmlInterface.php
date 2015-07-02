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
     * @param PaginateInterface $paginate
     * @return string
     */
    public function toString(PaginateInterface $paginate);
}