<?php

namespace Document;
//use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;


use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ODM\Document(collection="blog_post")
 */
class Post
{

    /**
     * @ODM\Id
     * 
     */
    protected $id;

    /**
     * @ODM\String
     * @ODM\UniqueIndex
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
     * @ODM\ReferenceOne(targetDocument="Document\User",cascade="update,merge",inversedBy="posts")
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

    function setUser(User $user) {
        $this->user = $user;
        $this->user->addPost($this);
    }

    function __toString() {
        return $this->title;
    }

    static function loadValidatorMetadata(ClassMetadata $metadatas) {
        $metadatas->addPropertyConstraint("body", new Length(array('min'=>10,'max'=>1000)));
        $metadatas->addPropertyConstraint("title", new Length(array('min'=>5,'max'=>255)));
        $metadatas->addConstraint(new Unique(array('fields'  => 'title')));
    }

}
