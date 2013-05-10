<?php

namespace Mparaiso\Provider;

use Silex\ServiceProviderInterface;
use Doctrine\ORM\Mapping\Driver\DriverChain;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\MongoDB\Connection;
use Exception;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mparaiso\Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Silex\Application;

class DoctrineODMServiceProvider implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Application $app) {
        $app['odm.options.default'] = array(
            "server" => NULL,
            "options" => array(),
        );
        $app['odm.options'] = array();
        $app['odm.manager_registry'] = $app->share(function($app) {
                    
                });
        // main document manager
        $app['odm.dm'] = $app->share(function ($app) {
                    foreach ($app['odm.driver.configs'] as $name => $options) {
                        $driver = DoctrineODMServiceProvider::getMetadataDriver($options['type'], $options['path']);
                        $app['odm.chain_driver']->addDriver($driver, $options['namespace']);
                        if ($name === "default") {
                            $app['odm.chain_driver']->setDefaultDriver($driver);
                        }
                    }
                    $app['odm.config']->setMetadataDriverImpl($app['odm.chain_driver']);
                    $dm = DocumentManager::create($app['odm.connection'], $app['odm.config']);
                    return $dm;
                });
        // connection class
        $app['odm.connection.class'] = function () {
                    return '\Doctrine\MongoDB\Connection';
                };
        // connection instance
        $app['odm.connection'] = $app->share(function ($app) {
                    $app['odm.options'] = array_merge($app['odm.options.default'], $app['odm.options']);
                    $class = $app['odm.connection.class'];
                    $conn = new $class($app['odm.options']['server'], $app['odm.options']['options']);
                    return $conn;
                });
        $app['odm.proxy_dir'] = function () {
                    return sys_get_temp_dir();
                };
        $app['odm.proxy_namespace'] = 'Proxies';
        $app['odm.hydrator_dir'] = function () {
                    return sys_get_temp_dir();
                };
        $app['odm.hydrator_namespace'] = 'Hydrators';

        $app['odm.chain_driver'] = $app->share(function ($app) {
                    return new MappingDriverChain;
                });
        $app['odm.driver.configs'] = array();
        $app['odm.config'] = $app->share(function ($app) {
                    $config = new Configuration();
                    //$config->setProxyDir($app['odm.proxy_dir']);
                    $config->setProxyNamespace($app['odm.proxy_namespace']);
                    $config->setHydratorDir($app['odm.hydrator_dir']);
                    $config->setHydratorNamespace($app['odm.hydrator_namespace']);
                    $config->setAutoGenerateHydratorClasses($app['debug']);
                    //$config->setAutoGenerateProxyClasses($app['debug']);
                    //$config->set
                    return $config;
                });
    }

    static function getMetadataDriver($type = "annotations", $classpath) {
        switch ($type) {
            case "yaml":
                return new YamlDriver($classpath);
                break;
            case "xml":
                return new XmlDriver($classpath);
                break;
            default:
                AnnotationDriver::registerAnnotationClasses();
                return AnnotationDriver::create($classpath);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app) {
        // TODO: Implement boot() method.
    }

}
