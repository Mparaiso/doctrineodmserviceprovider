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
    const POST_BEFORE_DELETE = "post_before_delete";
    const POST_AFTER_DELETE = "post_after_delete";

    function __construct(Base $service) {
        $this->service = $service;
    }

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
                $app['dispatcher']->dispatch(self::POST_BEFORE_CREATE, new GenericEvent($model, array(
                  'form' => $form, 'app'  => $app)));
                $app['odm.dm']->persist($model);
                $app['odm.dm']->flush();
                $app['dispatcher']->dispatch(self::POST_AFTER_CREATE, new GenericEvent($model, array(
                  'form' => $form, 'app'  => $app)));
                $app['session']->getFlashBag()->add('success', "Resource with title \" " . $model->getTitle() . " \" successfully created");

                return $app->redirect($app['url_generator']->generate('post_read', array('id' => $model->getId())));
            }
        }
        return $app['twig']->render('blog.post.create.html.twig', array(
              "form" => $form->createView(),
        ));
    }

    /**
     * update a post
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
                $app['dispatcher']->dispatch(self::POST_BEFORE_UPDATE, new GenericEvent($model, array(
                  'form' => $form, 'app'  => $app)));
                $app['odm.dm']->persist($model);
                $app['odm.dm']->flush();
                $app['dispatcher']->dispatch(self::POST_AFTER_UPDATE, new GenericEvent($model, array(
                  'form' => $form, 'app'  => $app)));
                $app['session']->getFlashBag()->add('success', "Resource with id \" " . $model->getTitle() . " \" updated successfully !");
                return $app->redirect($app['url_generator']->generate('post_read', array('id' => $model->getId())));
            }
        }
        return $app['twig']->render('blog.post.update.html.twig', array(
              "form" => $form->createView(),
              "post" => $model
        ));
    }

    /**
     * delete a post
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param \Silex\Application $app
     * @param type $id
     * @return type
     */
    function postDelete(Request $req, Application $app, $id) {
        $post = $app['odm.dm']->find("Document\Post", $id);
        if ($post === NULL) {
            $app->abort(404);
        }
        if ($req->getMethod() === "POST") {
            $app['dispatcher']->dispatch(self::POST_BEFORE_CREATE, new GenericEvent($post, array('app' => $app)));
            $app['odm.dm']->remove($post);
            $app['odm.dm']->flush();
            $app['dispatcher']->dispatch(self::POST_AFTER_DELETE, new GenericEvent($post, array('app' => $app)));
            $app['session']->getFlashBag()->add('success', "Resource with id \" " . $post->gettitle() . " \" deleted successfully !");
            return $app->redirect($app['url_generator']->generate('home'));
        }
        return $app['twig']->render('blog.post.delete.html.twig', array(
              "post" => $post,
        ));
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