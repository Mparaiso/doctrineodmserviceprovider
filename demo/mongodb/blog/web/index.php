<?php

$autoload = require __DIR__ . "/../vendor/autoload.php";

$autoload->add("", __DIR__ . "/../app");

$app = new App(array(
  "debug" => getenv('ODM_BLOG_DEMO_ENV') === "development" ? TRUE : FALSE
    )
);

$app['http_cache']->run();
