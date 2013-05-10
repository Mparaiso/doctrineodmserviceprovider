<?php

namespace Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="blog_post")
 */
class Post
{

    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\String
     */
    protected $title;

    /**
     * @ODM\String
     */
    protected $body;

    /**
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * @ODM\ReferenceOne(targetDocument="Mparaiso\User\Entity\User",cascade="all",inversedBy="posts")
     */
    protected $user;

    function __construct() {
        
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getId() {
        return $this->id;
    }

    function getUser() {
        return $this->user;
    }

    function setUser($user) {
        $this->user = $user;
    }

}
