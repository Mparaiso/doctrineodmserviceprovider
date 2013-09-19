<?php

namespace Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Mparaiso\User\Entity\Base\Role as BaseRole;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Role
 *
 * @ODM\Document(collection="blog_roles")
 */
class Role extends BaseRole implements RoleInterface
{

    /**
     * @var integer
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    static function loadValidatorMetadata(ClassMetadata $metadatas) {
        $metadatas->addConstraint(new Unique(array('fields' => 'name')));
        $metadatas->addConstraint(new Unique(array('fields' => 'role')));
        $metadatas->addPropertyConstraint("name", new Length(array('min' => 4, "max" => 100)));
        $metadatas->addPropertyConstraint("role", new Length(array('min' => 6, "max" => 100)));
        $metadatas->addPropertyConstraint("role", new Regex(array('pattern' => "/^ROLE\_/","message"=>"The value must begin by 'ROLE_'")));
    }

}