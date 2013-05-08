<?php

namespace Mparaiso\Provider;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\SchemaTool;


class DoctrineOrmServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    protected $app;

    protected function setUp()
    {
        parent::setUp();
        $this->app = getApp();
    }

    function testApp()
    {
        $this->assertNotNull($this->app);
    }

    function testServiceProvider()
    {
        $this->assertNotNull($this->app["orm.em"]);
    }

    function testCreateSchema()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em   = $this->app["orm.em"];
        $tool = new SchemaTool($em);
        //@note @doctrine générer les fichiers de classe à partir de métadonnées
        /* generate entity classes */
        $dmf = new DisconnectedClassMetadataFactory();
        $dmf->setEntityManager($em);
        $metadatas = $dmf->getAllMetadata();
        //print_r($metadatas);
        $generator = new EntityGenerator();
        $generator->setGenerateAnnotations(TRUE);
        $generator->setGenerateStubMethods(TRUE);
        $generator->setRegenerateEntityIfExists(TRUE);
        $generator->setUpdateEntityIfExists(TRUE);
        $generator->generate($metadatas, ROOT_TEST_DIR);
        $generator->setNumSpaces(4);
        $this->assertFileExists(ROOT_TEST_DIR . "/Entity/Post.php");
        /* @note @doctrine générer la base de donnée à partir des métadonnées */
        /* @see Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand */
        /* generate database */
        $tool->dropSchema($metadatas);
        $tool->createSchema($metadatas);
        $post = new \Entity\Post;
        $post->setTitle("the title");
        $em->persist($post);
        $em->flush();
        $this->assertInternalType("int", $post->getId());
    }


}