<?php
namespace WScore\Pagination;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;

class Pager
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pagerKey = '_page';

    /**
     * @var string
     */
    private $limitKey = '_limit';

    /**
     * @var array
     */
    private $inputs = [];

    /**
     * @var Inputs
     */
    private $inputObject;

    /**
     * @var array
     */
    private $default = [];

    /**
     * @param array $default
     */
    public function __construct($default = [])
    {
        $this->default = $default + [
                $this->pagerKey => 1,
                $this->limitKey => 20
            ];
    }

    /**
     * @API
     * @param ServerRequestInterface $request
     * @return $this
     */
    public function withRequest($request)
    {
        $self = clone($this);
        $self->request = $request;
        $self->setSessionName();
        $self->loadPageKey();
        return $self;
    }

    /**
     *
     */
    private function loadPageKey()
    {
        $query = $this->request->getQueryParams();
        if (!array_key_exists($this->pagerKey, $query)) {
            $this->inputs = $query;
        } else {
            $this->inputs = $this->loadFromSession($query);
        }
        $this->inputs += $this->default;
        $this->saveToSession();
    }

    /**
     * @param array $query
     * @return array
     */
    private function loadFromSession($query)
    {
        // get saved inputs from session.
        $name = $this->getSessionName();
        if (class_exists(RequestHelper::class)) {
            $saved = RequestHelper::getSession($this->request, $name, []);
        } else {
            $saved = array_key_exists($name, $_SESSION) ? $_SESSION[$name] : [];
        }
        // check if _page is specified in $query. if so, replace it with the saved value.
        if (isset($query[$this->pagerKey])) {
            $saved[$this->pagerKey] = (int)$query[$this->pagerKey];
        }
        return $saved;
    }

    /**
     * saves $inputs to session.
     */
    private function saveToSession()
    {
        if (class_exists(RequestHelper::class)) {
            RequestHelper::setSession($this->request, $this->getSessionName(), $this->inputs);
        } else {
            $_SESSION[$this->getSessionName()] = $this->inputs;
        }

    }

    /**
     *
     */
    private function setSessionName()
    {
        $server     = $this->request->getServerParams();
        $script     = isset($server['PATH_INFO']) ? $server['PATH_INFO'] : __FILE__;
        $this->name = 'pager-' . md5($script);
    }

    /**
     * @return string
     */
    private function getSessionName()
    {
        return $this->name;
    }

    /**
     * @API
     * @param \Closure $closure
     * @return mixed
     */
    public function call($closure)
    {
        $inputs = new Inputs($this->inputs);
        $inputs->pagerKey = $this->pagerKey;
        $inputs->limitKey = $this->limitKey;
        $this->inputObject = $inputs;
        return $closure($inputs);
    }

    /**
     * @param ToStringInterface $html
     * @return ToStringInterface
     */
    public function toHtml($html)
    {
        if (!isset($this->inputObject)) {
            throw new \BadMethodCallException('must call before html.');
        }
        return $html->withRequestAndInputs($this->request, $this->inputObject);
    }
}