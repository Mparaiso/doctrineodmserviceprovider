<?php

require __DIR__."/tests/Bootstrap.php";

$app = Bootstrap::getApp();
$em = $app["odm.dm"];
$app->boot();
$app['odm.boot_commands']();
$app["console"]->run();