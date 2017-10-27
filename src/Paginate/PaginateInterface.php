<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2017/10/21
 * Time: 16:07
 */
namespace Tuum\Pagination\Paginate;

use Tuum\Pagination\Inputs;

interface PaginateInterface
{
    /**
     * @param Inputs $inputs
     * @return PaginateInterface
     */
    public function setInputs(Inputs $inputs);

    /**
     * @return Page[]|\Iterator
     */
    public function getIterator();

    /**
     * @return Page
     */
    public function getFirstPage();

    /**
     * @return Page
     */
    public function getLastPage();

    /**
     * @return Page
     */
    public function getNextPage();

    /**
     * @return Page
     */
    public function getPrevPage();

    /**
     * @return string
     */
    public function __toString();
}