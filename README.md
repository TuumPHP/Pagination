Generic Pagination
================

a generic pagination class for PSR-7. 

Designed to keep the current page number and form input in session to simplify query code. 

Also provides flexibile pagination HTML generators. 

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TuumPHP/Pagination/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TuumPHP/Pagination/?branch=master)

PSR: PSR-1, PSR-2, PSR-4, and PSR-7.

### License

MIT license

### installation

please use composer to install WScore/Pagination package. 

```sh
$ composer require "tuum/pagination"
```


Getting Started with a Sample Code
----

### sample HTML form

Let's start with an HTML form for a pagination, for example; 

```html
<form>
  <input type="text" name="type" />
  <input type="integer" name="num" />
  <input type="submit" />
</form>
```

Please note that there should be **no `_page` variables which indicates the page number**. 

### constructing a pager

To instantiate a Pager class, 

```php
use WScore\Pagination\Pager;

// construction
$pager = new Pager(['_limit' => 15]);

// set up pager using Psr-7 ServerRequestInterface.
$pager = $pager->withRequest($request);
// or from globa. 
$pager = $pager->withQuery($_GET, '/find');
```

The pager object will store the query data (i.e. $_GET) to session for the subsequent requests if the session is already started. 

### paginating a query

Then, call a `Pager::call` method with a `closure` whose first argument is an `Inputs` object. 

```php
/** @var $inputs WScore\Pagination\Inputs */ 
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

The `type` and `num` values are either taken from the form input (if no _page is present), or from a session data (if _page is present). 

### generating pagination HTML 

There is a simplified class, `Pagination`, which can generate  pagination HTML for Twitter's bootstrap ver3. 

```php
use WScore\Pagination\Html\Factory\Pagination;
$pages = Pagination::forge();
$pages->num_links = 3;
$pages->label['first'] = '|<<';
$pages->aria['first'] = 'the first page';

// current code works like,
$pages = $pages->call($pager, function(Inputs $input) {...});
// but, maybe in the future,
$pages = $pages->withInputs($inputs);

// in the view. 
echo $pages->toHtml();
```

* [ ] [add sample image here...]

Technical Details
-----

### about the _page variable

The page key, `_page`, is the key. 

#### without _page

When the `_page` variable is present in a query, the Pager will store all the query data (i.e. $_GET) into the session. The page number is default to 1. 

#### _page with page number

Requesting with **only the page number** will restore the query values (type and num) from the session, and set the offset value  from the page number. For instance, 

```
GET /find?_page=2
```

will set offset, `(_page-1)*_limit`, with the page number being `2`. 


#### query with only _page

Requesting with **`_page` but no page number** will restore the page number and other parameters from the session. For instance, 

```
GET /find?_page
```

will set offset to the page number of last request. 


### setting a total

The pager does not know how to get a total; please supply the total count in the closure inside the call method usging `Inputs::setTotal` method; 

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
	if (!is_string($v) && !is_numeric($v)) {
        $v = '';
    } elseif (strpos($v, "\0") !== false) {
        $v = '';
    } elseif (!mb_check_encoding($v, 'UTF-8')) {
        $v = '';
    }
};
```

Generating Pagination Html
----

There are two principal interfaces to generate a pagination HTML: `PaginateInterface` and `ToHtmlInterface`. 

### `PaginateInterface` objects

The PaginateInterface is responsible to construct the basic elements of pagination; first, prev, next, last, and each pages. In some cases, you may or may not want first elements. 

```php
$paginate = (new Paginate)->withInputs($inputs);
$pages    = $paginate->toArray();
```

the `toArray` method returns an array, consisted of;

```php
$array = array(
	[ 'rel' => 'first', 
	  'href' => 'test?_page=1', 
	  'aria' => 'first page' ],
	...
);
```

where, 

*   `rel`: shows relation to the current page. Either of 'first', 'next', 'prev', 'last', or numeric page numbers. 
*   `href`: url to the page. 
*   `aria`: for aria-label.


There are 3 implementations of PaginateInterface:

*   `Paginate`
*   `PaginateMini`
*   `PaginateNext`

* [ ] supply sample image of pagination for each class.



### `ToHtmlInterface` objects

The `ToHtmlInterface ` objects takes a `PaginateInterface` object and convert into an HTML. Currently, there is only one implementation: `ToHtmlBootstrap`.

```php
echo ToHtmlBootstrap::forge()->withPaginate($paginate)->__toString();
```

