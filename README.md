Generic Pagination
================

a generic pagination class for PSR-7. 

Designed to keep the current page number and form input in session to simplify query code. 
Also a flexibility of pagination output and html/css format is in mind.   

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TuumPHP/Pagination/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TuumPHP/Pagination/?branch=master)

### License

MIT license

Getting Started
----

### installation

please use composer to install WScore/Pagination package. 

```sh
$ composer require "wscore/pagination"
```


### sample code

First, instantiate a Pager class. 

```php
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;

// construction
$pager = new Pager(['_limit' => 15]);

// set up pager using Psr-7 ServerRequestInterface.
$pager = $pager->withRequest($request);
// or from global data. 
$pager = $pager->withQuery($_GET, '/find');
```

Then, call a `Pager::call` method with a `closure` whose first argument is an `Inputs` object. 

```php
// query 
$inputs = $pager->call(
    function(Inputs $inputs) use($pdo) {
        // query the PDO!
        $found = $pdo->prepare("SELECT * FROM tbl WHERE type=? and num>? OFFSET ? LIMIT ?")
            ->execute([
                $inputs->get('type'),
                $inputs->get('num'),
                $inputs->getOffset(),
                $inputs->getLimit(),
            ])
            ->fetchAll();
        $inputs->setList($found);
    });
$found = $inputs->getList();
$type  = $inputs->get('type');
```

There is a default bootstrap pagination html. 

```php
use WScore\Pagination\Html\Paginate;

$inputs->paginate(new Paginate());
echo $inputs->__toString();
```

The `$inputs` object holds the information to construct a query. You can return anything from the closure; it will be passed back to you from the `Pager::call` method. 


Constructing an HTML Form
-----

The page key, `_page`, is the key. 

### initial query

Construct a query form **without `_page` key**. 

```html
<form>
<input type="text" name="type" />
<input type="integer" name="num" />
<input type="submit" />
</form>
```

Pager will store the query data (i.e. $_GET) to session for the subsequent requests. 

### query with page number 

Get request **with only the page number** will set the offset according to the page. For instance, 

```
GET /find?_page=2
```

will set offset according to the page #2. 

For other parameters, such as `type` and `num`, the values are restored from the session; thus supplying the same value as the initial query. 

### query with only _page

Get request with **`_page` but no page number** will restore the page number and other parameters from the session. For instance, 

```
GET /find?_page
```

will set offset to the page number of last request. 

Other Information
-----

### setting a total

The pager does not know how to get a total; please supply the total count in the closure for the call method; 

```php
// query 
$inputs = $pager->call(
    function(Inputs $inputs) use($pdo) {
        // calculate total
        $inputs->setTotal(
            $pdo->prepare("SELECT COUNT(*) FROM tbl WHERE type=? and num>? ")
                ->execute([
                    $inputs->get('type'),
                    $inputs->get('num')
                ])
                ->fetchColumn()
        );
        // query the PDO!
        $inputs->setList($pdo->prepare("..."));
    });
```

### security

As a default, the input values are validated to contain no nulls as well as a valid UTF-8 string. 

To change the validation, you can pass it at the construction of `Pager` as;

```php
$pager = new Pager([
    'validator' => function(&$v) {
        $v = 'validate=' . $v;
    },
]);
```

or you can simply set it like,

```php
$pager->validator = function(&$v) {
        $v = 'validate=' . $v;
    },
]);
```

FYI: this is the default closure. 

```php
function (&$v) {
    if (strpos($v, "\0") !== false) {
        $v = '';
    } elseif (!mb_check_encoding($v, 'UTF-8')) {
        $v = '';
    }
};
```

Output Pagination Html
----

### `PaginateInterface` interface

To create html pagination component, create an object implementing `PaginateInterface` and supply it to the Inputs object;

```php
$inputs->paginate(new Pagination);
```

There are 3 implementations of PaginateInterface:

*   `Paginate`
*   `PaginateMini`
*   `PaginateNext`

> TODO: supply sample image of pagination for each class.

#### `PaginateInterface::toArray` method

In PaginateInterface, it uses toArray method to construct an array of pages, which must contain following:

*   `rel`: shows relation to the current page. Either of 'first', 'next', 'prev', 'last', or numeric page numbers. 
*   `href`: url to the page. 
*   `aria`: for aria-label.

as such, the array may look like;

```php
$pages = array(
    ['rel' => 'first', 'href' => '/t?_page=1', 'aria' => 'first page'],
    ...
);
```

#### `PaginateInterface::__toString` method

The `__toString` method converts the array from `toArray` method to HTML using an object implementing `ToHtmlInterface`. The `ToHtmlBootstrap` class is used as a default which converts array into HTML for Bootstrap CSS. 

To use other HTML/CSS style, provide an object implementing the `ToHtmlInterface`, for example `ToMyHtml`, as;


```php
$inputs->paginate(new PaginateMini(new ToMyHtml()));
echo $inputs->__toString(); // outputs pagination html
```

### modifying `Paginate` object

Many of the configuration of Paginate objects can be done by accessing public properties. 

To change the default number of pages in Pagination, do as follows; 

```php
$paginate = new Paginate();
$paginate->num_links = 2;
$paginate->aria_label = [
    'first' => '最初', // in Japanese
];
$inputs->paginate($paginate);
```