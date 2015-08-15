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
     * @param Inputs $inputs
     * @return $this
     */
    public function withInputs(Inputs $inputs);

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
}