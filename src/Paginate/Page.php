<?php
namespace Tuum\Pagination\Paginate;

class Page
{
    /**
     * @var int
     */
    private $currPage;

    /**
     * set $page to 0 to disable the page.
     *
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $pagerKey;

    /**
     * Page constructor.
     *
     * @param string $key
     * @param int $currPage
     * @param int $page
     */
    public function __construct($key, $currPage, $page)
    {
        $this->pagerKey = $key;
        $this->currPage = (int) $currPage;
        $this->page = (int) $page;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page ?: '';
    }

    /**
     * @return bool
     */
    public function isCurrent()
    {
        return $this->currPage === $this->page;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->page;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->isDisabled() || $this->isCurrent()) {
            return '#';
        }
        return "?{$this->pagerKey}={$this->page}";
    }
}