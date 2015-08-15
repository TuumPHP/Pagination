<?php
namespace Tuum\Pagination\Factory;

class PageJa extends Pagination
{
    public static $aria = [
        'first' => '最初のページ',
        'prev'  => '前のページ',
        'next'  => '次のページ',
        'last'  => '最後のページ',
    ];

    public static $label = [
        'first' => '≪',
        'prev'  => '前',
        'next'  => '次',
        'last'  => '≫',
    ];

}