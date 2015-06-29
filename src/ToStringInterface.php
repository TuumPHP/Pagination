<?php
namespace WScore\Pagination;

/**
 * Created by PhpStorm.
 * User: asao
 * Date: 15/06/19
 * Time: 12:54
 */
interface ToStringInterface
{
    /**
     * @param string $path
     * @param Inputs $inputs
     * @return ToStringInterface
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