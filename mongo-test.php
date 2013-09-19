<?php

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Entity\Post;


$autoload = require(__DIR__ . "/vendor/autoload.php");
$autoload->add("", __DIR__ . "/tests/");

AnnotationDriver::registerAnnotationClasses();

$driver = AnnotationDriver::create(__DIR__ . "/tests/Entity");

$configuration = new Configuration();
$configuration->setMetadataDriverImpl($driver);
$configuration->setHydratorDir(sys_get_temp_dir() . '/Hydrators');
$configuration->setAutoGenerateHydratorClasses(true);
$configuration->setHydratorNamespace('Hydrators');
$configuration->setProxyDir(sys_get_temp_dir() . '/Proxy');
$configuration->setProxyNamespace("Proxy");
$configuration->setAutoGenerateProxyClasses(true);
$configuration->setDefaultDB("silex-wiki");
$connection = new Connection('mongodb://camus:defender@paulo.mongohq.com:10012/silex-wiki');

$dm = DocumentManager::create($connection, $configuration);
$post = new Post;
$post->setTitle("post title");
$post->setAuthor('post author');
$dm->persist($post);
$dm->flush($post);
