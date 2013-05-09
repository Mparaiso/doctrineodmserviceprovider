<?php

namespace Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="doctrineodm")
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

    function __construct()
    {

    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }
}
