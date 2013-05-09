<?php
use Silex\ServiceProviderInterface;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Mparaiso\Provider\ConsoleServiceProvider;
use Mparaiso\Provider\SimpleUserServiceProvider;
use Mparaiso\Provider\RouteConfigServiceProvider;
use Mparaiso\Provider\DoctrineODMServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Form\Post as PostType;
use Document\Post;

class Config implements ServiceProviderInterface, ControllerProviderInterface
{


    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        /* @var $controllers \Silex\ControllerCollection */
        // home post list
        $controllers->match("/", function (Application $app) {
            $dm = $app['odm.dm'];
            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $posts = $dm->getRepository('Document\Post')->findBy(array(), array("createdAt" => "DESC"));
            return $app['twig']->render('blog.post.index.html.twig', array(
                "posts" => $posts,
            ));
        })->bind("home");
        // create posts
        $controllers->match("/post/create", function (Request $req, Application $app) {
            $model = new Post();
            $type = new PostType();
            $form = $app['form.factory']->create($type, $model);
            if ("POST" === $req->getMethod()) {
                $form->bind($req);
                if ($form->isValid()) {
                    $model->setCreatedAt(new \DateTime());
                    $app['odm.dm']->persist($model);
                    $app['odm.dm']->flush();
                    return $app->redirect($app['url_generator']->generate('home'));
                }
            }
            return $app['twig']->render('blog.post.create.html.twig', array(
                "form" => $form->createView(),
            ));
        })->bind("post_create");
        $controllers->match("/post/read/{id}", function ($id, Application $app) {
            $post = $app['odm.dm']->find('Document\Post', $id);
            $post === NULL AND $app->abort(404, 'Not found');
            return $app['twig']->render('blog.post.read.html.twig', array(
                "post" => $post,
            ));
        })->bind('post_read')->assert('id', '\w+');
        return $controllers;
    }

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
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
            "security.firewalls" => array(
                "secured" => array(
                    "pattern"   => "^/",
                    "anonymous" => TRUE,
                )
            )
        ));
        $app->register(new DoctrineODMServiceProvider, array(
            'odm.driver.configs'       => $app->share(
                function ($app) {
                    return array(
                        "default"       => array(
                            "type"      => "annotations",
                            "path"      => array(__DIR__ . '/Document'),
                            "namespace" => "Document"
                        ),
                        "user.base"     => array(
                            "type"      => "yaml",
                            "path"      => $app['mp.user.resource.mongodb-odm.base'],
                            "namespace" => 'Mparaiso\User\Entity\Base',
                        ),
                        "user.concrete" => array(
                            "type"      => "yaml",
                            "path"      => $app['mp.user.resource.mongodb-odm.concrete'],
                            "namespace" => 'Mparaiso\User\Entity'
                        )
                    );
                }),
            'odm.document_classes_dir' => __DIR__ . '/Document',
            'odm.proxy_dir'            => __DIR__ . '/Proxies',
            'odm.hydrator_dir'         => __DIR__ . '/Hydrators',
        ));
        $app->register(new RouteConfigServiceProvider);
        $app->register(new SimpleUserServiceProvider, array(
            "mp.user.manager_type" => "doctrine/mongodb-odm",
            "mp.user.om"           => $app->share(function ($app) {
                return $app['odm.dm'];
            }),
        ));

        $app['layout'] = $app->share(function ($app) {
            return $app['mp.user.template.layout'];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
