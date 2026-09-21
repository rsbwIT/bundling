<?php
$f = 'c:\xampp\htdocs\bundling2\resources\views\layout\layoutDashboard.blade.php';
$c = file_get_contents($f);
// Match my specific @if ... @endif that wrap exactly ONE <li class="nav-item">...</li>
$c = preg_replace('/@if\(\\\\App\\\\Helpers\\\\AksesHelper::cek(?:Any)?\([^)]+\)\)\s*(<li class="nav-item">.*?<\/li>)\s*@endif/is', '$1', $c);
file_put_contents($f, $c);
echo "Done";
