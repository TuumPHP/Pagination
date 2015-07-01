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
     * @API
     * @return array
     */
    public function toArray();

    /**
     * @API
     * @return string
     */
    public function __toString();
}