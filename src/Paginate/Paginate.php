<?php
namespace Tuum\Pagination\Paginate;

use Tuum\Pagination\Inputs;

class Paginate implements \IteratorAggregate, PaginateInterface
{
    /**
     * @var int
     */
    protected $currPage;

    /**
     * @var int
     */
    private $firstPage;

    /**
     * @var int
     */
    private $lastPage;

    /**
     * @var string
     */
    private $pagerKey = '_page';

    /**
     * @var int
     */
    public $numLinks = 5;

    /**
     * Paginate constructor.
     *
     * @param int    $currPage
     * @param int    $lastPage
     * @param string $pagerKey
     * @param int    $firstPage
     */
    public function __construct($currPage, $lastPage, $pagerKey = '', $firstPage = 1)
    {
        $this->currPage  = $currPage;
        $this->firstPage = $firstPage;
        $this->lastPage  = $lastPage;
        $this->pagerKey  = $pagerKey ?: $this->pagerKey;
    }

    /**
     * @param Inputs $inputs
     * @return Paginate
     */
    public static function forge(Inputs $inputs)
    {
        $self = new self(
            $inputs->getPage(),
            $inputs->calcLastPage(),
            $inputs->pagerKey,
            $inputs->calcFirstPage()
            );
        return $self;
    }
    
    /**
     * @param Inputs $inputs
     * @return Paginate
     */
    public function setInputs(Inputs $inputs)
    {
        $this->currPage  = $inputs->getPage();
        $this->firstPage = $inputs->calcFirstPage();
        $this->lastPage  = $inputs->calcLastPage();
        $this->pagerKey  = $inputs->pagerKey;

        return $this;
    }

    /**
     * @return Page[]|\Iterator
     */
    public function getIterator(): \Traversable
    {
        $pages = $this->calcPageList();
        foreach($pages as $p) {
            yield $p;
        }
    }

    /**
     * @return Page[]
     */
    private function calcPageList()
    {
        $start    = $this->calcStart();
        $last     = $this->calcLast();

        $pages = [];
        if ($p = $this->getExtraStart()) {
            $pages[] = $p;
        }
        for ($p = $start; $p <= $last; $p++) {
            $pages[$p] = $this->forgePage($p);
        }
        if ($p = $this->getExtraLast()) {
            $pages[] = $p;
        }
        return $pages;
    }

    /**
     * @return int
     */
    private function calcStart()
    {
        $half = (int) ($this->numLinks / 2);
        $start = max($this->currPage - $half, $this->firstPage + 1);
        $maybe = max($this->lastPage - 1 - $this->numLinks, $this->firstPage + 1);
        $start = min($start, $maybe);
        return $start;
    }

    /**
     * @return bool|Page
     */    
    private function getExtraStart()
    {
        $start = $this->calcStart();
        if ($start < $this->firstPage + 2) {
            return false;
        }
        if ($start === $this->firstPage + 2) {
            return $this->forgePage(2);
        }
        return $this->forgePage(null);
    }

    /**
     * @return int
     */
    private function calcLast()
    {
        $numLinks = $this->numLinks - 1 + ($this->getExtraStart() ? 0: 1);
        $last = min($this->calcStart() + $numLinks, $this->lastPage - 1);
        return $last;
    }

    private function getExtraLast()
    {
        $last = $this->calcLast();
        if ($last > $this->lastPage - 2) {
            return false;
        }
        if ($last === $this->lastPage - 2) {
            return $this->forgePage($this->lastPage - 1);
        }
        return $this->forgePage(null);
    }
    
    /**
     * @param int $page
     * @return Page
     */
    private function forgePage($page)
    {
        return new Page($this->pagerKey, $this->currPage, $page);
    }

    /**
     * @return Page
     */
    public function getFirstPage()
    {
        return $this->forgePage($this->firstPage);
    }

    /**
     * @return Page
     */
    public function getLastPage()
    {
        return $this->forgePage($this->lastPage);
    }

    /**
     * @return Page
     */    
    public function getNextPage()
    {
        $page = min($this->currPage + 1, $this->lastPage);
        return $this->forgePage($page);
    }

    /**
     * @return Page
     */
    public function getPrevPage()
    {
        $page = max($this->currPage - 1, $this->firstPage);
        return $this->forgePage($page);
    }
}