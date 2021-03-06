<?php
namespace Tuum\Pagination;

use \Closure;
use Psr\Http\Message\ServerRequestInterface;

class Pager
{
    /**
     * a validation closure for $query input.
     *
     * @var \Closure
     */
    public $validator;

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
     * default values for input query ($this->inputs).
     *
     * @var array
     */
    private $defaultInputs = [];

    /**
     * path for the query.
     *
     * @var string
     */
    private $path;

    /**
     * @var Inputs
     */
    private $inputObject;

    /**
     * @param array        $default
     * @param null|Inputs  $inputObject
     */
    public function __construct(array $default = [], $inputObject = null)
    {
        $this->defaultInputs = $default + [
                $this->pagerKey => 1,
                $this->limitKey => 20
            ];
        $this->inputObject   = $inputObject;
    }

    /**
     * @param string $name
     * @param array  $default
     * @param null   $inputObject
     * @return Pager
     */
    public static function forge($name = '', array $default = [], $inputObject = null)
    {
        $self = new self($default, $inputObject);
        $self->name = $name ?: 'pager-' . $name;
        
        return $self;
    }

    /**
     * set a closure for validation.
     *
     * in PHP, sort of...
     * array_walk_recursive($_GET, $validator)
     *
     * @param Closure $validator
     */
    public function useValidator($validator)
    {
        $this->validator = $validator;
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
        return $this->withQuery(
            $request->getQueryParams(),
            $request->getUri()->getPath()
        );
    }

    /**
     * set up pager using the query data ($_GET) and pathInfo.
     *
     * @API
     * @param array $query query like $_GET
     * @param null  $path  path info
     * @return Pager
     */
    public function withQuery(array $query, $path = null)
    {
        $self = clone($this);
        $path = $path ?: htmlspecialchars($_SERVER['PATH_INFO'], ENT_QUOTES, 'UTF-8');
        $self->setSessionName($path);
        $self->loadPageKey($query);
        return $self;
    }

    /**
     * set query input data based on $query, or from session.
     *
     * @param array $query
     */
    private function loadPageKey($query)
    {
        if (!array_key_exists($this->pagerKey, $query)) {
            $this->inputs = $this->secureInput($query);
        } else {
            $this->inputs = $this->loadFromSession($query);
        }
        $this->inputs += $this->defaultInputs;
        $this->saveToSession();
    }

    /**
     * at least check if the input string does not have null-byte
     * and is a UTF-8 valid string.
     *
     * @param array $query
     * @return array
     */
    private function secureInput(array $query)
    {
        $secure = $this->validator ?: function (&$v) {
            if (!is_string($v) && !is_numeric($v)) {
                $v = '';
            } elseif (strpos($v, "\0") !== false) {
                $v = '';
            } elseif (!mb_check_encoding($v, 'UTF-8')) {
                $v = '';
            }
        };
        array_walk_recursive($query, $secure);
        return $query;
    }

    /**
     * load query input data from session.
     * the page number (pagerKey) is replaced with the input query's page.
     *
     * @param array $query
     * @return array
     */
    private function loadFromSession($query)
    {
        if (isset($_SESSION) && array_key_exists($this->name, $_SESSION)) {
            $loaded = $_SESSION[$this->name];
        } else {
            // no session. just use the $query.
            return $this->secureInput($query);
        }
        // check if _page is specified in $query. if so, replace it with the saved value.
        if (isset($query[$this->pagerKey]) && $query[$this->pagerKey] >= 1) {
            $loaded[$this->pagerKey] = (int)$query[$this->pagerKey];
        }
        return $loaded;
    }

    /**
     * saves $this->inputs to session.
     */
    private function saveToSession()
    {
        if (isset($_SESSION)) {
            $_SESSION[$this->name] = $this->inputs;
        }
    }

    /**
     * @param string $pathInfo
     */
    private function setSessionName($pathInfo)
    {
        $this->path = $pathInfo;
        $this->name = $this->name ?: 'pager-' . md5($pathInfo);
    }

    /**
     * call to construct your query based on the pager's input.
     *
     * @API
     * @param \Closure $closure
     * @return Inputs
     */
    public function call($closure)
    {
        $inputs = $this->forgeInputs();
        $closure($inputs);
        return $inputs;
    }

    /**
     * @return Inputs
     */
    public function getInputs()
    {
        return $this->forgeInputs();
    }

    /**
     * @return Inputs
     */
    private function forgeInputs()
    {
        $inputs           = $this->inputObject ? clone($this->inputObject) : new Inputs();
        $inputs->pagerKey = $this->pagerKey;
        $inputs->limitKey = $this->limitKey;
        $inputs->path     = $this->path;
        $inputs->inputs   = $this->inputs;

        return $inputs;
    }
}