<?php
$lines = file('c:\xampp\htdocs\bundling2\storage\logs\laravel.log');
echo implode('', array_slice($lines, -100));
