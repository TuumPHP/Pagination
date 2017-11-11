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
     * @param bool|string $true
     * @param bool|string $false
     * @return bool|string
     */
    public function isCurrent($true = true, $false = false)
    {
        return $this->currPage === $this->page 
            ? $true: $false;
    }

    /**
     * @param bool|string $true
     * @param bool|string $false
     * @return bool|string
     */
    public function isDisabled($true = true, $false = false)
    {
        return !$this->page
            ? $true: $false;
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