<?php $f = fopen('storage/logs/laravel.log', 'r'); fseek($f, -50000, SEEK_END); echo fread($f, 50000); ?>
