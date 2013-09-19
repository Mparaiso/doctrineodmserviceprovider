Doctrine ODM Service Provider
=============================

[![Build Status](https://travis-ci.org/Mparaiso/doctrineodmserviceprovider.png?branch=master)](https://travis-ci.org/Mparaiso/doctrineodmserviceprovider)

Doctrine ODM service provider for silex
---------------------------------------

Brings NoSQL databases to Silex Framework.

author : M.Paraiso
contact: mparaiso@online.fr

status: work in progress

helps use Doctrine ODM with silex

supports MongoDB

### BASIC USAGE

 $app = new \Silex\Application();
        $app->register(new DoctrineODMMongoDBServiceProvider, array(
            "odm.connection.server" => getenv('ODM_MONGODB_TEST_CONNECTION_STRING'), // mongodb connection string
            "odm.connection.dbname" => getenv('ODM_MONGODB_TEST_DATABASE_NAME'), // dbname
            "odm.connection.options" => array('connect' => TRUE), // connection options
            "odm.proxy_dir" => __DIR__ . "/Proxy", // Proxy dir
            "odm.driver.configs" => array(
                "default" => array(
                    "namespace" => "Entity", // Entity Namespace
                    "path" => __DIR__ . "/Entity", // Entity Directory
                    "type" => "annotations" // Metadata driver ( 'yaml','xml' or 'annotations' )
                )
            )
        ));

### CHANGE LOG
