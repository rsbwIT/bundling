<?php
require 'c:/xampp/htdocs/bundling2/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/bundling2/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$menus = DB::table('menu_bundlings')->get();
foreach($menus as $m) {
    echo $m->id.' - '.$m->name.' - '.$m->url."\n";
}
