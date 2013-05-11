<?php

namespace Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mparaiso\CodeGeneration\Service\ICRUDService;

class Post extends Base implements ICRUDService
{

    function save($entity) {
        if ($entity->getCreatedAt() == NULL)
            $entity->setCreatedAt(new \DateTime);
        return parent::save($entity);
    }

}