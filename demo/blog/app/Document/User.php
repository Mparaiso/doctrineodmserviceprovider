<?php

namespace Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Mparaiso\User\Entity\Base\User as BaseUser;
use Mparaiso\User\Entity\Base\Role as BaseRole;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Represent a user document , has many roles , has many posts
 * 
 * @ODM\Document(collection="blog_user")
 * 
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
     * @ODM\ReferenceMany(targetDocument="Document\Post",cascade="all",mappedBy="user")
     * @var ArrayCollection
     */
    protected $posts;

    function getPosts() {
        return $this->posts;
    }

    function addPost(Post $post) {
        $this->posts[] = $post;
    }

    function removePost(Post $post) {
        $this->posts->removeElement($post);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function addRole(BaseRole $role) {
        $this->roles[] = $role;
    }

    public function removeRole(BaseRole $role) {
        $this->roles->removeElement($role);
    }

    public function getRoles() {
        return $this->roles->toArray();
    }

    function __construct() {
        parent::__construct();
        $this->roles = new ArrayCollection;
        $this->posts = new ArrayCollection;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

}