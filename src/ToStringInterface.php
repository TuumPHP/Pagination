<?php
namespace WScore\Pagination;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Created by PhpStorm.
 * User: asao
 * Date: 15/06/19
 * Time: 12:54
 */
interface ToStringInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param Inputs                 $inputs
     * @return ToStringInterface
     */
    public function withRequestAndInputs($request, $inputs);

    /**
     * @API
     * @return string
     */
    public function __toString();
}