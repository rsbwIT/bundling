<?php
$lines = file('c:\xampp\htdocs\bundling2\storage\logs\laravel.log');
foreach(array_reverse($lines) as $line) {
    if(strpos($line, 'local.ERROR') !== false || strpos($line, 'Exception') !== false) {
        echo $line;
        break;
    }
}
