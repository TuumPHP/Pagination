<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\ToStringInterface;

class ToBootstrap extends AbstractBootstrap implements ToStringInterface
{
    /**
     * @var array
     */
    protected $options = [
        'first'     => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }
}
