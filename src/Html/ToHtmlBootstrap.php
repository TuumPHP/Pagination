<?php
namespace WScore\Pagination\Html;

class ToHtmlBootstrap implements ToHtmlInterface
{
    /**
     * @var array
     */
    public $labels = [
        'first'     => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];

    /**
     * @var string
     */
    public $ul_class = 'pagination';

    public $default_type = 'disable';

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->labels = $options + $this->labels;
    }

    /**
     * @param array $pages
     * @return string
     */
    public function toString(array $pages)
    {
        $html = '';
        foreach ($pages as $info) {
            $html .= $this->listItem($info);
        }

        return "<ul class=\"{$this->ul_class}\">\n{$html}</ul>\n";
    }

    /**
     * @param array $info
     * @return string
     */
    private function listItem(array $info)
    {
        $label = isset($info['rel']) ? $info['rel'] : '';
        $href  = isset($info['href']) ? $info['href'] : '';
        $aria  = isset($info['aria']) ? $info['aria'] : '';
        return $this->bootLi($label, $href, $aria);
    }

    /**
     * @param string $rel
     * @param string $href
     * @param string $aria
     * @return string
     */
    private function bootLi($rel, $href, $aria)
    {
        $label = isset($this->labels[$rel]) ? $this->labels[$rel] : $rel;
        $srLbl = $aria ? " aria-label=\"{$aria}\"" : '';
        if ($href != '#') {
            $html = "<li><a href='{$href}'";
            $html .= $srLbl . " >{$label}</a></li>\n";
        } elseif (is_numeric($rel)) {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
        } else {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        }
        return $html;
    }
}