Pagination
==========

A generic pagination package for classic offset/limit style.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TuumPHP/Pagination/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TuumPHP/Pagination/?branch=master)

Some interesting features maybe:

*   stores the current page and form inputs in session,
*   can use PSR-7 ServerRequestInterface object,
*   have a flexible pagination HTML generator.

### PSR

PSR-1, PSR-2, PSR-4, and PSR-7.

### License

MIT license

### installation

Please use composer to install WScore/Pagination package. 

```sh
$ composer require "tuum/pagination"
```


Getting Started with a Sample Code
----

The page key variable, `_page`, is the key. 

### sample HTML form

Let's start with an HTML form for a pagination, for example; 

```html
<form action="find">
  <input type="text" name="type" />
  <input type="integer" name="num" />
  <input type="submit" />
</form>
```

Please note that there should be **no `_page` variables**. 

### paginating a query

The simplest way to use a `Pagination` class that combines all the functionalities. 

To instantiate a `Pageinate` object, 

```php
use Tuum\Pagination\Inputs;
use Tuum\Pagination\Factory\Pagination;

$pages = Pagination::forge()->call(
    $request, // PSR-7 ServerRequestInterface object
    function(Inputs $inputs) use($pdo) {
        // sample query using PDO
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

The `Inputs::get*` methods should provides information to construct a query. The `Inputs::setList` method sets the found results inside. 

The `type` and `num` values are: 

* taken from the form input and then saved to a session if no `_page` variables are present in the input query, or 
* taken from a session data (if `_page` is present). 

### `_page` with page number

Requesting with **only the page number** will restore the query values (type and num) from the session, and set the offset value  from the page number. For instance, 

```
GET /find?_page=2
```

will set offset, `(_page-1)*_limit`, with the page number being `2`. 


### query with only `_page`

Requesting with **`_page` but no page number** will restore the page number and other parameters from the session. For instance, 

```
GET /find?_page
```

will set offset to the page number of last request. 


### generating pagination HTML 

The Pagination class implements a `__toString` method to output pagination HTML string. As a default, the Pagination object outputs following style of pagination HTML for Bootstrap ver3. 

```php
$pager = Pagination::forge()...;
echo $htmlPages->__toString(); // outputs the html.
```

![sample paginate HTML](./toHtmlMini.jpg)

It is possible to change the pagination and HTML/CSS style by providing appropriate objects when constructing the `Pagination` object. 

Technical Details
-----

### setting a total

The pager does not know how to get a total; please supply the total count in the closure inside the call method usging `Inputs::setTotal` method; 

```php
// query 
$pager = $pager->call(
    $request, 
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
$pager = Pagination::forge();
$pager->pager->useValidator(function(&$v) {
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

There are two principal interfaces to generate a pagination HTML: `PaginateInterface` and `ToHtmlInterface`. Use them like:

```php
$pager = Pagination::forge(
    new MyPaginateClass,
    new MyToHtmlClass
);
```

### `PaginateInterface` objects

The PaginateInterface is responsible to construct the basic elements of pagination; first, prev, next, last, and each pages. In some cases, you may or may not want first elements. 

```php
$paginate = (new MyPaginateClass)->withInputs($inputs);
$pages    = $paginate->toArray();
```

the `toArray` method returns an array, consisted of;

```php
$array = array(
	[ 'rel' => 'first', 
	  'href' => 'test?_page=1', 
	  'page' => 1,
	  'label' => 'First',
	  'aria' => 'first page'
	], 
	[], // denotes an empty cell
	[...],
);
```

where, 

*   `rel`: shows relation to the current page. Either of 'first', 'next', 'prev', 'last', or numeric page numbers. 
*   `href`: url to the page. 
*   `page`: the page number.
*   `label`: labels used in the HTML. If not set, it uses the default label as defined in `ToHtmlInterface` objects.
*   `aria`: for aria-label.
*   An empty array denotes a spacer, to be used to display such as [...]. 

Following implementations of PaginateInterface are available:

*   `PaginateFull`
*   `PaginateMini`



### `ToHtmlInterface` objects

The `ToHtmlInterface ` objects takes a `PaginateInterface` object and convert into an HTML. Currently, there is only one implementation: `ToHtmlBootstrap`.

```php
echo ToHtmlBootstrap::forge()->withPaginate($paginate)->__toString();
```

