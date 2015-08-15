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
     * @return $this
     */
    public function withPaginate(PaginateInterface $paginate);

    /**
     * construct HTML (i.e. string) from PaginateInterface::toArray.
     *
     * @return string
     */
    public function toString();

    /**
     * @return string
     */
    public function __toString();
}