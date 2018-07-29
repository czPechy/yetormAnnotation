<?php
/*
 * YetOrmAnnotation
 * (c) 2018 Martin Pecha | Pecha.pro
 */

ini_set('display_errors', 1);

// installed in project
if(is_file(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
}
// run in development
if(is_file(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}
require __DIR__ . '/Cli/Control.php';

(new \czPechy\YetOrmAnnotation\Cli\Control())->run();