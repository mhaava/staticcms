<?php
    define('BASEPATH', realpath(__DIR__.'/../../').DIRECTORY_SEPARATOR);
    error_reporting(E_ERROR | E_PARSE);
    require_once BASEPATH. '/app/Router.php';
    use \App\Router;
    Router::index();
