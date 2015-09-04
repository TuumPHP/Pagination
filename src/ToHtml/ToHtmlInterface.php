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
     * @return $this
     */
    public function withPaginate(PaginateInterface $paginate);

    /**
     * @param array $labels
     * @return $this
     */
    public function setLabels(array $labels);

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