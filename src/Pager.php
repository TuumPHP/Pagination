<?php
namespace WScore\Pagination;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\SessionStorageInterface;

class Pager
{
    /**
     * name for session storage key. 
     * 
     * @var string
     */
    private $name;

    /**
     * query key name for setting page number. 
     * 
     * @var string
     */
    private $pagerKey = '_page';

    /**
     * query key name for setting limit (per page number).
     * 
     * @var string
     */
    private $limitKey = '_limit';

    /**
     * query for pager. maybe get from $_GET, or from session. 
     * 
     * @var array
     */
    private $inputs = [];

    /**
     * object representation of the input query for pager. 
     * will instantiated after call method. 
     * 
     * @var Inputs
     */
    private $inputObject;

    /**
     * default values. 
     * 
     * @var array
     */
    private $default = [];

    /**
     * path for the query. 
     * 
     * @var string
     */
    private $path;

    /**
     * session storage manager if exists. 
     * 
     * @var SessionStorageInterface
     */
    private $session;

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
     * set up pager using the PSR7 server request.
     * 
     * @API
     * @param ServerRequestInterface $request
     * @return $this
     */
    public function withRequest($request)
    {
        $self = clone($this);
        if (class_exists(RequestHelper::class)) {
            $self->session = RequestHelper::getSessionMgr($request);
        }
        $self->setSessionName($request->getUri()->getPath());
        $self->loadPageKey($request->getQueryParams());
        return $self;
    }

    /**
     * set up pager using the query data ($_GET) and pathInfo. 
     * 
     * @API
     * @param array $query    query like $_GET
     * @param null  $path     path info
     * @return Pager
     */    
    public function withQuery(array $query, $path=null)
    {
        $self = clone($this);
        $path = $path ?: htmlspecialchars($_SERVER['PATH_INFO'], ENT_QUOTES, 'UTF-8');
        $self->setSessionName($path);
        $self->loadPageKey($query);
        return $self;
    }
    
    /**
     * @param array $query
     */
    private function loadPageKey($query)
    {
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
        if ($this->session) {
            $saved = $this->session->get($this->name, []);
        } else {
            $saved = array_key_exists($this->name, $_SESSION) ? $_SESSION[$this->name] : [];
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
        if ($this->session) {
            $this->session->set($this->name, $this->inputs);
        } else {
            $_SESSION[$this->name] = $this->inputs;
        }

    }

    /**
     * @param string $pathInfo
     */
    private function setSessionName($pathInfo)
    {
        $this->path = $pathInfo;
        $this->name = 'pager-' . md5($pathInfo);
    }

    /**
     * call to construct your query based on the pager's input. 
     * 
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
     * set up ToStringInterface objects to output html pagination. 
     * 
     * @API
     * @param ToStringInterface $html
     * @return ToStringInterface
     */
    public function toHtml($html)
    {
        $inputs = $this->inputObject ?: new Inputs($this->inputs);
        return $html->withRequestAndInputs($this->path, $inputs);
    }
}