<?php
namespace Tuum\Pagination\Factory;

use Tuum\Pagination\Html\Paginate;
use Tuum\Pagination\Html\ToHtmlBootstrap;
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Pager;

class Pagination
{
    protected $aria = [];

    protected $label = [];

    protected $num_links = 3;

    protected $limit = 15;

    protected $paginate = Paginate::class;

    protected $toHtml = ToHtmlBootstrap::class;

    /**
     * @return static
     */
    public static function forge()
    {
        return new static();
    }

    /**
     * @param array $aria
     * @return $this
     */
    public function aria(array $aria)
    {
        $this->aria = $aria;
        return $this;
    }

    /**
     * @param array $label
     * @return $this
     */
    public function label(array $label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param int $num
     * @return $this
     */
    public function numLinks($num)
    {
        $this->num_links = $num;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $paginate_class
     * @return $this
     */
    public function paginate($paginate_class)
    {
        $this->paginate = $paginate_class;
        return $this;
    }

    /**
     * @param string $toHtml_class
     * @return $this
     */
    public function toHtml($toHtml_class)
    {
        $this->toHtml = $toHtml_class;
        return $this;
    }

    /**
     * @return Pager
     */
    public function getPager()
    {
        $class  = $this->toHtml;
        $label  = $this->label;
        $toHtml = new $class($label);

        $class    = $this->paginate;
        $paginate = new $class($toHtml);
        $paginate->aria_label += $this->aria;
        $paginate->num_links = $this->num_links;

        $inputs = new Inputs($paginate);
        $pager  = new Pager($inputs, ['_limit' => $this->limit]);
        return $pager;
    }
}