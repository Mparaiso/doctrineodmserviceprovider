<?php

namespace Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Mparaiso\User\Entity\Base\Role as BaseRole;
use Symfony\Component\Security\Core\Role\RoleInterface;

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

}