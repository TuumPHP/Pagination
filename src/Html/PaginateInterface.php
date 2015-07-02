<?php
namespace Tuum\Pagination\Html;

use Tuum\Pagination\Inputs;

/**
 * Interface PaginateInterface
 *
 * @package WScore\Pagination\Html
 */
interface PaginateInterface
{
    /**
     * @param string $path
     * @param Inputs $inputs
     * @return PaginateInterface
     */
    public function withRequestAndInputs($path, $inputs);

    /**
     * constructs an array of page information.
     *   $pages = array(
     *     [...page info...], [...]
     *   );
     *
     * where page info is consisted of the following.
     *   - rel : relation to the current page, such as 'first'.
     *   - href: uri for the page.
     *   - aria: human readable description.
     *
     * @API
     * @return array
     */
    public function toArray();

    /**
     * @API
     * @param ToHtmlInterface $toHtml
     * @return string
     */
    public function toHtml($toHtml = null);

    /**
     * @API
     * @return string
     */
    public function __toString();
}