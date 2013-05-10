<?php

use Controller\DefaultController;
use Mparaiso\Provider\ConsoleServiceProvider;
use Mparaiso\Provider\DoctrineODMMongoDBServiceProvider;
use Mparaiso\Provider\RouteConfigServiceProvider;
use Mparaiso\Provider\SimpleUserServiceProvider;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

class Config implements ServiceProviderInterface, ControllerProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app) {
        $controllers = $app['controllers_factory'];
        /* @var $controllers ControllerCollection */
        $controllers->match("/", 'controller.default:postIndex')->bind("home");
        $controllers->match("/post/create", 'controller.default:postCreate')->bind("post_create");
        $controllers->match("/post/update/{id}", 'controller.default:postUpdate')
                ->before($app['must_be_post_owner'])
                ->bind("post_update");
        $controllers->match("/post/delete/{id}", 'controller.default:postDelete')
                ->before($app['must_be_post_owner'])
                ->bind("post_delete");
        $controllers->match("/post/read/{id}", 'controller.default:postRead')->bind('post_read');
        return $controllers;
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app) {
        $app->register(new MonologServiceProvider,
                array(
            'monolog.logfile' => __DIR__ . '/../temp/' . date('Y-m-d') . ".txt",
        ));
        $app->register(new HttpCacheServiceProvider(),
                array(
            "http_cache.cache_dir" => __DIR__ . '/../temp/http-cache'
        ));
        $app->register(new SessionServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new TwigServiceProvider(),
                array(
            "twig.path" => __DIR__ . '/Resources/views/',
            "twig.options" => array(
                "cache" => __DIR__ . '/../temp/twig'
            )
        ));
        $app->register(new TranslationServiceProvider());
        $app->register(new ConsoleServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new ValidatorServiceProvider(),
                array(
            'validator.validator_service_ids' => array(),
        ));
        $app->register(new SecurityServiceProvider(),
                array(
            "security.access_rules" => array(
                array('^/profile', 'ROLE_USER'),
                array('^/login-check', 'ROLE_USER'),
                array('^/logout', "ROLE_USER"),
            ),
            "security.firewalls" => $app->share(
                    function ($app) {
                        return array(
                            "secured" => array(
                                "pattern" => "^/",
                                "anonymous" => TRUE,
                                "form" => array(
                                    "login_path" => "/login",
                                    "check_path" => "/login-check"
                                ),
                                "logout" => array(
                                    "logout_path" => "/logout"
                                ),
                                "users" => $app['mp.user.user_provider'],
                            )
                        );
                    })));
        $app->register(new DoctrineODMMongoDBServiceProvider,
                array(
            "odm.connection.options" => array("db" => "blog_database"),
            'odm.driver.configs' => $app->share(
                    function ($app) {
                        return array(
                            "default" => array(
                                "type" => "annotations",
                                "path" => array(__DIR__ . '/Document'),
                                "namespace" => "Document"
                            ),
                            "user.base" => array(
                                "type" => "yaml",
                                "path" => $app['mp.user.resource.mongodb-odm.base'],
                                "namespace" => 'Mparaiso\User\Entity\Base',
                            ),
                                /* "user.concrete" => array(
                                  "type" => "yaml",
                                  "path" => $app['mp.user.resource.mongodb-odm.concrete'],
                                  "namespace" => 'Mparaiso\User\Entity'
                                  ) */
                        );
                    }),
            'odm.document_classes_dir' => __DIR__ . '/Document',
            'odm.proxy_dir' => __DIR__ . '/Proxy',
            "odm.proxy_namespace" => "Proxy",
            'odm.hydrator_dir' => __DIR__ . '/Hydrator',
            "odm.hydrator_namespace" => "Hydrator"
        ));
        $app->register(new RouteConfigServiceProvider);
        $app->register(new SimpleUserServiceProvider,
                array(
            'mp.user.user.class' => "Document\User",
            "mp.user.role.class" => "Document\Role",
            "mp.user.manager_type" => "doctrine/mongodb-odm",
            "mp.user.om" => $app->share(function ($app) {
                        return $app['odm.dm'];
                    }),
            "mp.user.manager_registry" => $app->share(function ($app) {
                        return $app['odm.manager_registry'];
                    }),
            'mp.user.user_provider' => $app->share(function ($app) {
                        $service = new $app['mp.user.user_provider.class'](
                                $app['mp.user.manager_registry'], $app['mp.user.user.class'],
                                $app['mp.user.user_provider.property']
                        );
                        return $service;
                    })
        ));

        $app['controller.default'] = $app->share(function() {
                    return new DefaultController();
                }
        );
        $app['listener.post_before_create'] = $app->protect(function(GenericEvent $event) {
                    $post = $event->getSubject();
                    $app = $event->getArgument("app");
                    $user = $app['security']->getToken()->getUser();
                    if ($user === NULL)
                        $app->abort(500, "User not found");
                    $post->setUser($user);
                }
        );
        $app['listener.post_before_update'] = $app->protect(function(GenericEvent $event)use($app) {
                    $post = $event->getSubject();
                    /* @var $post \Document\Post */
                    $user = $app['security']->getToken()->getUser();
                    if ($post->getUser() != $user) {
                        $app->abort(500, "You cannot edit/delete this resource");
                    }
                }
        );

        $app['must_be_post_owner'] = $app->protect(function(Request $req)use($app) {
                    $postId = $req->attributes->get('id');
                    $user = $app['security']->getToken()->getUser();
                    $post = $app['odm.dm']->getRepository('Document\Post')->findOneBy(array('id' => $postId));
                    if ($post->getUser() !== $user) {
                        $app['logger']->alert("Access denied for user $user to post with id $postId ");
                        $app->abort(500, 'You cant access this resource !');
                    }
                }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app) {
        $app->on(DefaultController::POST_BEFORE_CREATE, $app['listener.post_before_create']);
        $app->on(DefaultController::POST_BEFORE_UPDATE, $app['listener.post_before_update']);
        $app->on(DefaultController::POST_BEFORE_DELETE, $app['listener.post_before_update']);
    }

}

