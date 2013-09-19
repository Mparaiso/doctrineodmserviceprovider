<?php

namespace Mparaiso\Provider;

use Entity\Post;
use PHPUnit_Framework_TestCase;
use Doctrine\ODM\MongoDB\DocumentManager;


class DoctrineODMMongoDBServiceProviderTest extends \PHPUnit_Framework_TestCase
{


    function testRegister()
    {
        $app = \Bootstrap::getApp();
        $dm = $app['odm.dm'];
        /* @var DocumentManager $dm */
        $post = new  Post;
        $post->setContent("content of the post");
        $post->setTitle("title of the post");
        $post->setAuthor("author of the post");
        $post->setCreated(new \DateTime());
        $dm->persist($post);
        $dm->flush();
        $this->assertNotNull($post->getId());
        $count = $dm->getRepository('Entity\Post')->findAll()->count();
        $this->assertEquals(1, $count);
        $dm->remove($post);
        $dm->flush();
    }


}