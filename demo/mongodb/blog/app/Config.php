<?php

use Controller\DefaultController;
use Mparaiso\CodeGeneration\Controller\CRUD as CrudController;
use Mparaiso\Provider\ConsoleServiceProvider;
use Mparaiso\Provider\CrudServiceProvider;
use Mparaiso\Provider\DoctrineODMMongoDBServiceProvider;
use Mparaiso\Provider\RouteConfigServiceProvider;
use Mparaiso\Provider\SimpleUserServiceProvider;
use Service\User as UserService;
use Service\Post as PostService;
use Service\Role as RoleService;
use Silex\Application;
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

class Config implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function register(Application $app) {
        $app->register(new MonologServiceProvider, array(
          'monolog.logfile' => __DIR__ . '/../temp/' . date('Y-m-d') . ".txt",
        ));
        $app->register(new HttpCacheServiceProvider(), array(
          "http_cache.cache_dir" => __DIR__ . '/../temp/http-cache'
        ));
        $app->register(new SessionServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new TwigServiceProvider(), array(
          "twig.path"    => __DIR__ . '/Resources/views/',
          "twig.options" => array(
            "cache" => __DIR__ . '/../temp/twig'
          )
        ));
        $app->register(new TranslationServiceProvider());
        $app->register(new ConsoleServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new ValidatorServiceProvider(), array(
          'validator.validator_service_ids' => array(),
        ));
        $app->register(new SecurityServiceProvider(), array(
          "security.access_rules" => array(
            array('^/$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/post/read', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/register', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/', 'ROLE_USER'),
            array('^/admin', 'ROLE_USER'),
          ),
          "security.firewalls"    => $app->share(
              function ($app) {
                  return array(
                    "secured" => array(
                      "pattern"   => "^/",
                      "anonymous" => true,
                      "form"      => array(
                        "login_path" => "/login",
                        "check_path" => "/login-check"
                      ),
                      "logout"    => array(
                        "logout_path" => "/logout"
                      ),
                      "users"     => $app['mp.user.user_provider'],
                    )
                  );
              })));
        $app->register(new DoctrineODMMongoDBServiceProvider, array(
          "odm.connection.server"  => getenv('ODM_BLOG_DEMO_SERVER'),
          "odm.connection.options" => array("db" => "blog_database"),
          'odm.driver.configs'     => $app->share(
              function ($app) {
                  return array(
                    "default"   => array(
                      "type"      => "annotations",
                      "path"      => array(__DIR__ . '/Document'),
                      "namespace" => "Document"
                    ),
                    "user.base" => array(
                      "type"      => "yaml",
                      "path"      => $app['mp.user.resource.mongodb-odm.base'],
                      "namespace" => 'Mparaiso\User\Entity\Base',
                    ),
                  );
              }),
          'odm.document_classes_dir' => __DIR__ . '/Document',
          'odm.proxy_dir'            => __DIR__ . '/Proxy',
          "odm.proxy_namespace"      => "Proxy",
          'odm.hydrator_dir'         => __DIR__ . '/Hydrator',
          "odm.hydrator_namespace"   => "Hydrator"
        ));
        $app->register(new RouteConfigServiceProvider);
        $app->register(new SimpleUserServiceProvider, array(
          'mp.user.user.class'   => "Document\User",
          "mp.user.role.class"   => "Document\Role",
          "mp.user.manager_type" => "doctrine/mongodb-odm",
          "mp.user.om"           => $app->share(function ($app) {
                  return $app['odm.dm'];
              }),
          "mp.user.manager_registry" => $app->share(function ($app) {
                  return $app['odm.manager_registry'];
              }),
          'mp.user.user_provider' => $app->share(function ($app) {
                  $service = new $app['mp.user.user_provider.class'](
                      $app['mp.user.manager_registry'], $app['mp.user.user.class'], $app['mp.user.user_provider.property']
                  );
                  return $service;
              })
        ));

        $app->register(new CrudServiceProvider);

        // Services
        // Default Controller
        $app['controller.default'] = $app->share(function($app) {
                $service = $app["crud.service.post"];
                return new DefaultController($service);
            }
        );
        // post manager
        $app["crud.service.post"] = $app->share(function($app) {
                return new PostService($app['odm.dm'], $app['document.post']);
            });
        // post controller for administration
        $app["crud.controller.post"] = $app->share(function($app) {
                return new CrudController(array(
                  "resourceName"   => "post",
                  "service"        => $app['crud.service.post'],
                  "entityClass"    => $app['document.post'],
                  "formClass"      => $app['form.post'],
                  "templateLayout" => "blog.admin.layout.html.twig"
                ));
            });
        // user manager
        $app["crud.service.user"] = $app->share(function($app) {
                return new UserService($app['odm.dm'], $app['document.user']);
            });
        // user controller for administration
        $app["crud.controller.user"] = $app->share(function($app) {
                return new CrudController(array(
                  "resourceName"   => "user",
                  "service"        => $app['crud.service.user'],
                  "entityClass"    => $app['document.user'],
                  "formClass"      => $app['form.user'],
                  "templateLayout" => "blog.admin.layout.html.twig"
                ));
            });
        // role manager
        $app['crud.service.role'] = $app->share(function($app) {
                return new RoleService($app['odm.dm'], $app['document.role']);
            });
        $app['crud.controller.role'] = $app->share(function($app) {
                return new CrudController(array(
                  "resourceName"   => "role",
                  "service"        => $app['crud.service.role'],
                  "entityClass"    => $app['document.role'],
                  "formClass"      => $app['form.role'],
                  "templateLayout" => "blog.admin.layout.html.twig"
                ));
            });
        $app['document.post'] = '\Document\Post';
        $app['document.user'] = '\Document\User';
        $app['document.role'] = '\Document\Role';
        $app['form.post'] = '\Form\Post';
        $app['form.user'] = '\Form\User';
        $app['form.role'] = '\Form\Role';

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
                /* @var $post Post */
                $user = $app['security']->getToken()->getUser();
                if ($post->getUser() != $user) {
                    $app->abort(500, "You cannot edit/delete this resource");
                }
            }
        );

        $app['must_be_post_owner'] = $app->protect(function(Request $req)use($app) {
                $id = $req->attributes->get('id');
                $user = $app['security']->getToken()->getUser();
//                /* @var $user \Document\User */
//                $posts = $user->getPosts();
//                $predicate = $posts->exists(function($index, \Document\Post $post)use($id) {
//                        return $post->getId() == $id;
//                    });
//                if ($predicate == FALSE) {
//                    $app['logger']->alert("Access denied for user $user to post with id $id ");
//                    $app->abort(500, 'You cant access this resource !');
//                }
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

        $app->mount('/admin/', $app["crud.controller.post"]);
        $app->mount('/admin/', $app['crud.controller.user']);
        $app->mount('/admin/', $app['crud.controller.role']);
        $app->mount("/", $app["controller.default"]);
    }

}

