<?php

namespace Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Mparaiso\User\Entity\Base\User as BaseUser;
use Mparaiso\User\Entity\Base\Role;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ODM\Document(collection="blog_user")
 */
class User extends BaseUser implements AdvancedUserInterface
{

    /**
     * @ODM\Id
     */
    protected $id;

    /**
     *
     * @ODM\ReferenceMany(targetDocument="Document\Role",cascade="all")
     * @var Mparaiso\User\Entity\Base\Role
     */
    protected $roles;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function addRole(Role $role) {
        $this->roles[] = $role;
    }

    public function removeRole(Role $role) {
        $this->roles->removeElement($role);
    }

    public function getRoles() {
        return $this->roles->toArray();
    }

    function __construct() {
        parent::__construct();
        $this->roles = new ArrayCollection;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

}