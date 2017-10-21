<?php
namespace Tuum\Pagination\ToHtml;

use Tuum\Pagination\Paginate\PaginateInterface;

/**
 * Interface ToHtml
 *
 * @package WScore\Pagination\Html
 */
interface ToHtmlInterface
{
    /**
     * @param PaginateInterface $paginate
     * @return ToHtmlInterface
     */
    public function setPaginate(PaginateInterface $paginate);

    /**
     * @return string
     */
    public function __toString();
}