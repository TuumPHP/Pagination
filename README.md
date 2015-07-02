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
$ composer require "tuum/pagination"
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

$pages = $inputs->paginate(new Paginate());
echo $pages->__toString();
```


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
So, please start the session in prior. 

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
$pager = (new Pager())
    ->useValidator(function(&$v) {
        $v = 'validate=' . $v;
    });
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

### using `Pagination` builder

Pagination builder helps to construct a pagination object. examples:

```php
use Tuum\Pagination\Factory\Pagination;

$pages = Pagination::start()
    ->numLinks(3)
    ->label([
        'first' => '1st',
    ])
    ->aria([
        'first' => '1st page',
    ])
    ->pagination(new PaginateNext())
    ->getPaginate();
$pages = $inputs->paginate($pages);
echo $pages->__toString();
```

#### `PaginateInterface` objects

There are 3 implementations of PaginateInterface:

*   `Paginate`
*   `PaginateMini`
*   `PaginateNext`

> TODO: supply sample image of pagination for each class.


### inside the Pagination

There are two interfaces for outputing an HTML: 

*   `PaginateInterface` for creating an array of page information, and 
*   `ToHtmlInterface` for converting the array to HTML code. 

To create html pagination component, create an object implementing `PaginateInterface` and supply it to the Inputs object;

```php
class MyPagination implements PaginateInterface {...}
class MyHtml implements ToHtmlInterface {...}

$inputs= $pager->call(function...);

// convert to HTML
$pages = $inputs->paginate(new MyPagination);
$htmls = $pages->toHtml($new MyHtml);
```


#### `PaginateInterface::toArray` method

The main API of the `PaginateInterface` is the `toArray` method, which construct an array of pages with following information:

*   `rel`: shows relation to the current page. Either of 'first', 'next', 'prev', 'last', or numeric page numbers. 
*   `href`: url to the page. 
*   `aria`: for aria-label.

as such, the array may look like;

```php
$pages = array(
    [
        'rel' => 'first', 
        'href' => '/t?_page=1', 
        'aria' => 'first page'
    ], ...
);
```

#### `PaginateInterface::toHtml` method

The `PaginateInterface::toHtml` method takes an object implementing `ToHtmlInterface`, then converts the pages array into an HTML code. There is only one implementation of the `ToHtmlInterface`, `ToHtmlBootstrap`.

```php
$pages = $inputs->paginate(new Paginate());
$htmls = $pages->toHtml(new ToHtmlBootstrap());
echo $htmls;
```

The `PaginateInterface::__toString` method simply calls the `toHtml` method without arguments. 
