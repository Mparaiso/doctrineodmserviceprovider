<?php

namespace Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mparaiso\CodeGeneration\Service\ICRUDService;

/**
 * base class for services
 */
abstract class Base implements ICRUDService
{

    /**
     *
     * @var DocumentManager
     */
    protected $om;

    /**
     *
     * @var string
     */
    protected $class;

    function __construct(ObjectManager $om, $class) {
        $this->om = $om;
        $this->class = $class;
    }

    function count() {
        return count($this->findAll());
    }

    function save($entity) {
        $this->om->persist($entity);
        $this->om->flush();
        return $entity;
    }

    public function find($id) {
        return $this->om->find($this->class, $id);
    }

    public function findAll() {
        return $this->om->getRepository($this->class)->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        return $this->om->getRepository($this->class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria) {
        return $this->om->getRepository($this->class)->findOneBy($criteria);
    }

    public function getClassName() {
        return $this->class;
    }

    function delete($entity) {
        $this->om->remove($entity);
        $this->om->flush();
        return $entity;
    }

}