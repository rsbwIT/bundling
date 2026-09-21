<?php
$f = 'c:\xampp\htdocs\bundling2\resources\views\layout\layoutDashboard.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/<script>\s*document\.addEventListener\("DOMContentLoaded", function\(\) \{\s*\/\/ 1\. First, process all treeview parents.*?\}\);\s*<\/script>/is', '', $c);
file_put_contents($f, $c);
