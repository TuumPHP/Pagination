<?php
namespace Tuum\Pagination\Local;

use Tuum\Pagination\Pagination;

class PageJa extends Pagination
{
    public $aria = [
        'first' => '最初のページ',
        'prev'  => '前のページ',
        'next'  => '次のページ',
        'last'  => '最後のページ',
    ];

    public $label = [
        'first' => '≪',
        'prev'  => '前',
        'next'  => '次',
        'last'  => '≫',
    ];

}