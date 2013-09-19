<?php

use Mparaiso\Provider\DoctrineODMMongoDBServiceProvider;
use Mparaiso\Provider\ConsoleServiceProvider;

$autoload = require(__DIR__ . '/../vendor/autoload.php');
$autoload->add("", __DIR__);
$autoload->add("", __DIR__ . "/../src");

class Bootstrap
{

    static function getApp()
    {
        $app = new \Silex\Application();
        $app["debug"] = TRUE;
        $app["session.test"] = 1;
        $app->register(new ConsoleServiceProvider);
        $app->register(new DoctrineODMMongoDBServiceProvider, array(
            "odm.connection.server" => getenv('ODM_MONGODB_TEST_CONNECTION_STRING'),
            "odm.connection.dbname" => getenv('ODM_MONGODB_TEST_DATABASE_NAME'),
            "odm.connection.options" => array('connect' => TRUE),
            "odm.proxy_dir" => __DIR__ . "/Proxy",
            "odm.driver.configs" => array(
                "default" => array(
                    "namespace" => "Entity",
                    "path" => __DIR__ . "/Entity",
                    "type" => "annotations"
                )
            )
        ));

        return $app;
    }
}