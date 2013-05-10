<?php

namespace Controller;

use DateTime;
use Document\Post;
use Form\Post as PostType;
use Silex\Application;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

class DefaultController
{

    const POST_BEFORE_CREATE = "post_before_create";
    const POST_AFTER_CREATE = "post_after_create";
    const POST_BEFORE_UPDATE = "post_before_update";
    const POST_AFTER_UPDATE = "post_after_update";

    /**
     * list posts
     * @param Application $app
     * @return string
     */
    function postIndex(Application $app) {
        $dm = $app['odm.dm'];
        /* @var $dm DocumentManager */
        $posts = $dm->getRepository('Document\Post')->findBy(array(), array("createdAt" => "DESC"));
        return $app['twig']->render('blog.post.index.html.twig', array(
                    "posts" => $posts,
        ));
    }

    /**
     * create a post
     * @param \Controller\Request $req
     * @param Application $app
     * @return string
     */
    function postCreate(Request $req, Application $app) {
        $model = new Post();
        $type = new PostType();
        $form = $app['form.factory']->create($type, $model);
        if ("POST" === $req->getMethod()) {
            $form->bind($req);
            if ($form->isValid()) {
                $model->setCreatedAt(new DateTime());
                $app['dispatcher']->dispatch(self::POST_BEFORE_CREATE, new GenericEvent($model, array('form' => $form, 'app' => $app)));
                $app['odm.dm']->persist($model);
                $app['odm.dm']->flush();
                $app['dispatcher']->dispatch(self::POST_AFTER_CREATE, new GenericEvent($model, array('form' => $form, 'app' => $app)));
                return $app->redirect($app['url_generator']->generate('home'));
            }
        }
        return $app['twig']->render('blog.post.create.html.twig', array(
                    "form" => $form->createView(),
        ));
    }

    /**
     * create a post
     * @param \Controller\Request $req
     * @param Application $app
     * @return string
     */
    function postUpdate(Request $req, Application $app, $id) {
        $model = $app['odm.dm']->find("Document\Post", $id);
        if ($model === NULL)
            $app->abort(404);
        $type = new PostType();
        $form = $app['form.factory']->create($type, $model);
        if ("POST" === $req->getMethod()) {
            $form->bind($req);
            if ($form->isValid()) {
                $model->setCreatedAt(new DateTime());
                $app['dispatcher']->dispatch(self::POST_BEFORE_UPDATE, new GenericEvent($model, array('form' => $form, 'app' => $app)));
                $app['odm.dm']->persist($model);
                $app['odm.dm']->flush();
                $app['dispatcher']->dispatch(self::POST_AFTER_UPDATE, new GenericEvent($model, array('form' => $form, 'app' => $app)));
                return $app->redirect($app['url_generator']->generate('home'));
            }
        }
        return $app['twig']->render('blog.post.update.html.twig', array(
                    "form" => $form->createView(),
                    "id" => $id
        ));
    }

    function postDelete($id) {
        
    }

    /**
     * read a post
     * @param type $id
     * @param Application $app
     * @return string
     */
    function postRead($id, Application $app) {
        $post = $app['odm.dm']->find('Document\Post', $id);
        if ($post === NULL)
            $app->abort(404, 'Not found');
        return $app['twig']->render('blog.post.read.html.twig', array(
                    "post" => $post,
        ));
    }

}