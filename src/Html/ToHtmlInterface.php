<?php
namespace WScore\Pagination\Html;

/**
 * Interface ToHtml
 *
 * @package WScore\Pagination\Html
 */
interface ToHtmlInterface
{
    /**
     * @param array $pages
     * @return string
     */
    public function toString(array $pages);
}