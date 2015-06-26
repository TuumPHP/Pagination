Generic Pagination
================

a generic pagination class for PSR-7 and others. 

### License

MIT license

Getting Started
----

### installation

not registered to packagist, yet. 

### code

```php
use WScore\Pagination\Inputs;
use WScore\Pagination\Pager;

// construction
$pager = new Pager(['_limit' => 15]);

// set up pager using Psr-7 ServerRequestInterface.
$pager = $pager->withRequest($request);
// or from global data. 
$pager = $pager->withQuery($_GET, '/find');

// query 
$found = $pager->call(
    function(Inputs $inputs) use($pdo) {
        // query the PDO!
        return $pdo->prepare("
            SELECT * FROM tbl WHERE type=? and num>? OFFSET ? LIMIT ?
            ")
            ->execute([
                $inputs->get('type'),
                $inputs->get('num'),
                $inputs->getOffset(),
                $inputs->getLimit(),
            ])
            ->fetchAll();
    });
```

Requesting for Query
-----

The page key, `_page`, is the key. 

### initial query

Construct a query form **without _page key**. 

```html
<form>
<input type="text" name="type" />
<input type="integer" name="num" />
<input type="submit" />
</form>
```

Pager will store the query data (i.e. $_GET) to session for the subsequent requests. The offset will be always 0. 

### query with page number 

Get request **with only the page number** will set the offset according to the page. For instance, 

```
GET /find?_page=2
```

will set offset according to the page #2. 

For other parameters, such as `type` and `num`, the values are restored from the session; thus supplying the same value as the initial query. 

### query with only _page

Get request with _page but no page number will restore the page number and other parameters from the session. For instance, 

```
GET /find?_page
```

will set offset to the page number of last request. 

### setting total

The pager does not know how to get a total; please supply the total count in the closure for the call method; 

```php
// query 
$found = $pager->call(
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
        return $pdo->prepare("...");
    });
```


Pagination Component
----

To create html pagination component, create an object implementing ToStringInterface and supply it to the pager as;

```php
$pages = new ToHtml;
$pages = $pager->toHtml($pages);
echo $pages->__toString(); // outputs pagination html
```

### ToBoostrap and ToBootstrap3

There are already ToBootstrap class to construct pagination component for Bootstrap. 

```php
$pages = $pager->toHtml(new ToBootstrap([
        'top'       => '&laquo; first',
        'prev'      => '&lt; prev',
        'next'      => 'next &gt;',
        'last'      => 'last &raquo;',
        'num_links' => 3,
]));
echo $pages->__toString(); // outputs pagination html
```

ToBootstrap3 is under construction. 