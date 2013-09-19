<?php

namespace Mparaiso\Provider;

use Doctrine\Bundle\MongoDBBundle\Form\DoctrineMongoDBExtension;
use Doctrine\Bundle\MongoDBBundle\Logger\Logger;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\MongoDB\Configuration as ConnectionConfiguration;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Mparaiso\Doctrine\ODM\MongoDB\Command\InfoDoctrineODMCommand;
use Mparaiso\Doctrine\ODM\MongoDB\ManagerRegistry;
use Mparaiso\Doctrine\ODM\PimpleConstraintValidatorFactory;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use \Doctrine\ODM\MongoDB\Tools\Console\Command as MongoCommand;

class DoctrineODMMongoDBServiceProvider implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        // if true debug queries , regenerate proxies and hydrators.
        $app['odm.debug'] = function ($app) {
            return $app['debug'];
        };
        // connection config
        $app['odm.connection.configuration'] = $app->share(function ($app) {
                $config = new ConnectionConfiguration;
                if ($app['odm.debug'] === TRUE && isset($app['logger'])) {
                    $config->setLoggerCallable($app['odm.connection.log_function']);
                }
                return $config;
            }
        );
        // log function
        $app['odm.connection.log_function'] = $app->protect(
            function ($msg) use ($app) {
                return $app['odm.connection.logger']->logQuery($msg);
            }
        );
        //logger
        $app['odm.connection.logger'] = $app->share(function ($app) {
                return new Logger($app['logger']);
            }
        );
        // server connection string
        $app['odm.connection.server'] = null;
        // mongo client connection options
        $app['odm.connection.options'] = array();
        // multiple document managers
        $app['odm.manager_registry'] = $app->share(function ($app) {
            return new ManagerRegistry("manager_registry", array('default' => $app['odm.connection']), array(
                'default' => $app['odm.dm']), "default", "default");
        });
        // default document manager
        $app['odm.dm'] = $app->share(function ($app) {
            foreach ($app['odm.driver.configs'] as $name => $options) {
                $driver = DoctrineODMMongoDBServiceProvider::getMetadataDriver($options['type'], $options['path']);
                $app['odm.chain_driver']->addDriver($driver, $options['namespace']);
                if ($name === "default") {
                    $app['odm.chain_driver']->setDefaultDriver($driver);
                }
            }
            $app['odm.config']->setMetadataDriverImpl($app['odm.chain_driver']);
            $dm = DocumentManager::create($app['odm.connection'], $app['odm.config']);
            # fix the proxy serialization bug @TODO fix it
            $proxies = glob($app['odm.proxy_dir'] . "/__CG__*.php");
            foreach ($proxies as $proxy) {
                require($proxy);
            }
            return $dm;
        });
        $app["odm.mongoclient"] = $app->share(function ($app) {
            $client = new \MongoClient($app['odm.connection.server'], $app['odm.connection.options']);
            if (isset($app["odm.connection.dbname"])) {
                $client->selectDB($app["odm.connection.dbname"]);
            }
            return $client;

        });
        // mongo client  connection
        $app['odm.connection'] = $app->share(function ($app) {
            $conn = new Connection($app["odm.mongoclient"], $app['odm.connection.options'], $app['odm.connection.configuration']);
            return $conn;
        });
        // proxy classes dir
        $app['odm.proxy_dir'] = function () {
            return sys_get_temp_dir();
        };
        // proxy classes namespace
        $app['odm.proxy_namespace'] = 'Proxies';
        // hydrators dir
        $app['odm.hydrator_dir'] = function () {
            return sys_get_temp_dir();
        };
        // hydrator namespace
        $app['odm.hydrator_namespace'] = 'Hydrators';
        // main document manager driver
        $app['odm.chain_driver'] = $app->share(function ($app) {
            return new MappingDriverChain;
        });
        // driver configs 
        $app['odm.driver.configs'] = array();
        // main manager config
        $app['odm.config'] = $app->share(function ($app) {
            $config = new Configuration();
            $config->setProxyDir($app['odm.proxy_dir']);
            $config->setProxyNamespace($app['odm.proxy_namespace']);
            $config->setHydratorDir($app['odm.hydrator_dir']);
            $config->setHydratorNamespace($app['odm.hydrator_namespace']);
            $config->setAutoGenerateHydratorClasses($app['odm.debug']);
            $config->setAutoGenerateProxyClasses($app['odm.debug']);
            return $config;
        });
        // if you want to manage mongo db through the $app[console] , execute this function
        $app['odm.boot_commands'] = $app->protect(function () use ($app) {
            $app["console"]->getHelperSet()->set(new DocumentManagerHelper($app["odm.dm"]));
            $app['console']->add(new InfoDoctrineODMCommand);
            $app["console"]->add(new MongoCommand\QueryCommand);
            $app["console"]->add(new MongoCommand\GenerateDocumentsCommand);
            $app["console"]->add(new MongoCommand\GenerateHydratorsCommand());
            $app["console"]->add(new MongoCommand\GenerateProxiesCommand());
            $app["console"]->add(new MongoCommand\GenerateRepositoriesCommand());
            $app["console"]->add(new MongoCommand\ClearCache\MetadataCommand());
            $app["console"]->add(new MongoCommand\Schema\CreateCommand());
            $app["console"]->add(new MongoCommand\Schema\DropCommand());
            $app["console"]->add(new MongoCommand\Schema\UpdateCommand());

        });
        // unique document validator service
        $app['doctrine_odm.mongodb.unique'] = $app->share(function ($app) {
            return new UniqueEntityValidator($app['odm.manager_registry']);
        });
    }

    /**
     * Helper method to get metadata drivers
     * @param type $type
     * @param type $classpath
     * @return YamlDriver|XmlDriver
     */
    static function getMetadataDriver($type, $classpath)
    {
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
    public function boot(Application $app)
    {
        if (!isset($app['validator.validator_service_ids']))
            $app['validator.validator_service_ids'] = array();
        $arr = $app['validator.validator_service_ids'];
        $arr['doctrine_odm.mongodb.unique'] = 'doctrine_odm.mongodb.unique';
        if (!isset($arr['doctrine.orm.validator.unique']))
            $arr['doctrine.orm.validator.unique'] = 'doctrine_odm.mongodb.unique';
        $app['validator.validator_service_ids'] = $arr;

        // using form extensions
        if (isset($app['form.extensions'])) {
            $extensions = $app['form.extensions'];
            $extensions[] = new DoctrineMongoDBExtension($app['odm.manager_registry']);
            $app['form.extensions'] = $extensions;
        }
    }

}
