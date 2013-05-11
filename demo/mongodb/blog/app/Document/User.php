<?php

namespace Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Mparaiso\User\Entity\Base\User as BaseUser;
use Mparaiso\User\Entity\Base\Role as BaseRole;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


/**
 * Represent a user document , has many userRoles , has many posts
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
     * The user roles<br>
     * the field is explicitly named roles to avoid breaking the code<br>
     * @ODM\ReferenceMany(targetDocument="Document\Role",cascade="all",name="roles")
     * @var ArrayCollection
     */
    protected $userRoles;

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
        if (!$this->userRoles->contains($role))
            $this->userRoles[] = $role;
    }

    public function removeRole(BaseRole $role) {
        if ($this->userRoles->contains($role))
            $this->userRoles->removeElement($role);
    }

    public function getRoles() {
        return $this->userRoles->toArray();
    }

    /**
     * Get roles <br>
     * Fix the bug with Document\Entity Form collections that ask for a Collection and Not an Array<br>
     * @todo find another solution
     * @return ArrayCollection
     */
    public function getUserRoles() {
        return $this->userRoles;
    }

    function getRolesCollection() {
        return $this->userRoles;
    }

    function __construct() {
        parent::__construct();
        $this->userRoles = new ArrayCollection;
        $this->posts = new ArrayCollection;
    }

    public function setRoles($userRoles) {
        $this->userRoles = $userRoles;
    }

}