<?php
use Mparaiso\Provider\DoctrineORMServiceProvider;
use Mparaiso\Provider\ConsoleServiceProvider;

!defined('ROOT_TEST_DIR') AND define('ROOT_TEST_DIR', __DIR__);
$autoload = require(ROOT_TEST_DIR . '/../vendor/autoload.php');
$autoload->add("", ROOT_TEST_DIR . '/../src/');
$autoload->add("", ROOT_TEST_DIR . "/");
function getApp()
{
    $app = new \Silex\Application();
    $app["debug"]=TRUE;
    $app->register(new ConsoleServiceProvider);
    $app->register(new DoctrineORMServiceProvider, array(
        "orm.connection" =>
        array('driver' => "pdo_sqlite", 'path' => ROOT_TEST_DIR . '/database.sqlite'),
        "orm.driver.configs"    => array(
            "default" => array(
                "namespace"=>"Entity",
                "type"  => "yaml",
                "paths" => array(ROOT_TEST_DIR . '/doctrine'),
            )
        )
    ));

    return $app;
}