<?php
namespace WScore\Pagination\Html;

use Psr\Http\Message\ServerRequestInterface;
use WScore\Pagination\Inputs;

abstract class AbstractBootstrap
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var Inputs
     */
    protected $inputs;

    /**
     * @var int
     */
    protected $currPage;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    public $ul_class = 'pagination';

    /**
     * @API
     * @param ServerRequestInterface $request
     * @param Inputs                 $inputs
     * @return $this
     */
    public function withRequestAndInputs($request, $inputs)
    {
        $self           = clone($this);
        $self->request  = $request;
        $self->inputs   = $inputs;
        $self->currPage = $inputs->getCurrPage();
        return $self;
    }

    /**
     * @param array $info
     * @return string
     */
    abstract protected function listItem(array $info);

    /**
     * @param int $numLinks
     * @return array
     */
    abstract protected function calculatePagination($numLinks);
    
    /**
     * @return string
     */
    public function __toString()
    {
        $pages = $this->calculatePagination($this->options['num_links']);
        $html  = '';
        foreach ($pages as $info) {
            $html .= $this->listItem($info);
        }

        return "<ul class=\"{$this->ul_class}\">\n{$html}</ul>\n";
    }

    /**
     * @return string
     */
    protected function getRequestUri()
    {
        return $this->request->getUri()->getPath() . '?';
    }

    /**
     * @param $numLinks
     * @return array
     */
    protected function fillPages($numLinks)
    {
        $start = max($this->currPage - $numLinks, 1);
        $last  = min($this->currPage + $numLinks, $this->findLastPage($numLinks));

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[] = ['label' => $page, 'page' => $page, 'type' => 'active'];
        }
        return $pages;
    }

    /**
     * @param int $numLinks
     * @return int
     */
    protected function findLastPage($numLinks)
    {
        // total and perPage is set.
        $total = $this->inputs->getTotal();
        $pages = $this->inputs->getLimit();
        if (!is_null($total) && $pages) {
            return (integer)(ceil($total / $pages));
        }
        return $this->currPage + $numLinks;
    }

}