<?php
namespace tests\Pagination;

use Tuum\Pagination\Paginate\Paginate;
use Tuum\Pagination\ToHtml\ToBootstrap3;

require_once __DIR__ . '/../autoloader.php';

class ToBootstrap3Test extends \PHPUnit_Framework_TestCase
{
    public function test0()
    {
        $p     = new Paginate(7, 15);
        $h = new ToBootstrap3($p);
        $this->assertEquals('<ul class="pagination"><li class=""><a href="?_page=6" >&lt;</a></li>
<li><a href="?_page=1" >1</a></li>
<li class="disabled"><a href="#" >...</a></li><li><a href="?_page=5" >5</a></li>
<li><a href="?_page=6" >6</a></li>
<li class="active"><a href="#" >7<span class=\'sr-only\'>current</span></a></li>
<li><a href="?_page=8" >8</a></li>
<li><a href="?_page=9" >9</a></li>
<li class="disabled"><a href="#" >...</a></li><li><a href="?_page=15" >15</a></li>
<li class=""><a href="?_page=8" >&gt;</a></li>
</ul>', (string) $h);
    }
}
