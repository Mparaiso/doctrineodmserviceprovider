<?php

require "tests/bootstrap.php";

$app = getApp();
$em = $app["orm.em"];
$app["console"]->run();