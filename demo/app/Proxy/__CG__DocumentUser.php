<?php

namespace Proxy\__CG__\Document;

use Doctrine\ODM\MongoDB\Persisters\DocumentPersister;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ODM. DO NOT EDIT THIS FILE.
 */
class User extends \Document\User implements \Doctrine\ODM\MongoDB\Proxy\Proxy
{
    private $__documentPersister__;
    public $__identifier__;
    public $__isInitialized__ = false;
    public function __construct(DocumentPersister $documentPersister, $identifier)
    {
        $this->__documentPersister__ = $documentPersister;
        $this->__identifier__ = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->__documentPersister__) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->__documentPersister__->load($this->__identifier__, $this) === null) {
                throw \Doctrine\ODM\MongoDB\DocumentNotFoundException::documentNotFound(get_class($this), $this->__identifier__);
            }
            unset($this->__documentPersister__, $this->__identifier__);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getPosts()
    {
        $this->__load();
        return parent::getPosts();
    }

    public function addPost(\Document\Post $post)
    {
        $this->__load();
        return parent::addPost($post);
    }

    public function removePost(\Document\Post $post)
    {
        $this->__load();
        return parent::removePost($post);
    }

    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return $this->__identifier__;
        }
        $this->__load();
        return parent::getId();
    }

    public function addRole(\Mparaiso\User\Entity\Base\Role $role)
    {
        $this->__load();
        return parent::addRole($role);
    }

    public function removeRole(\Mparaiso\User\Entity\Base\Role $role)
    {
        $this->__load();
        return parent::removeRole($role);
    }

    public function getRoles()
    {
        $this->__load();
        return parent::getRoles();
    }

    public function setRoles($roles)
    {
        $this->__load();
        return parent::setRoles($roles);
    }

    public function setUsername($username)
    {
        $this->__load();
        return parent::setUsername($username);
    }

    public function getUsername()
    {
        $this->__load();
        return parent::getUsername();
    }

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
    }

    public function setEmail($email)
    {
        $this->__load();
        return parent::setEmail($email);
    }

    public function getEmail()
    {
        $this->__load();
        return parent::getEmail();
    }

    public function setSalt($salt)
    {
        $this->__load();
        return parent::setSalt($salt);
    }

    public function getSalt()
    {
        $this->__load();
        return parent::getSalt();
    }

    public function setAccountNonExpired($accountNonExpired)
    {
        $this->__load();
        return parent::setAccountNonExpired($accountNonExpired);
    }

    public function getAccountNonExpired()
    {
        $this->__load();
        return parent::getAccountNonExpired();
    }

    public function setAccountNonLocked($accountNonLocked)
    {
        $this->__load();
        return parent::setAccountNonLocked($accountNonLocked);
    }

    public function getAccountNonLocked()
    {
        $this->__load();
        return parent::getAccountNonLocked();
    }

    public function setCredentialsNonExpired($credentialsNonExpired)
    {
        $this->__load();
        return parent::setCredentialsNonExpired($credentialsNonExpired);
    }

    public function getCredentialsNonExpired()
    {
        $this->__load();
        return parent::getCredentialsNonExpired();
    }

    public function setEnabled($enabled)
    {
        $this->__load();
        return parent::setEnabled($enabled);
    }

    public function getEnabled()
    {
        $this->__load();
        return parent::getEnabled();
    }

    public function serialize()
    {
        $this->__load();
        return parent::serialize();
    }

    public function unserialize($serialized)
    {
        $this->__load();
        return parent::unserialize($serialized);
    }

    public function eraseCredentials()
    {
        $this->__load();
        return parent::eraseCredentials();
    }

    public function isAccountNonExpired()
    {
        $this->__load();
        return parent::isAccountNonExpired();
    }

    public function isAccountNonLocked()
    {
        $this->__load();
        return parent::isAccountNonLocked();
    }

    public function isCredentialsNonExpired()
    {
        $this->__load();
        return parent::isCredentialsNonExpired();
    }

    public function isEnabled()
    {
        $this->__load();
        return parent::isEnabled();
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'username', 'password', 'email', 'salt', 'accountNonExpired', 'accountNonLocked', 'credentialsNonExpired', 'enabled', 'id', 'roles', 'posts');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->__documentPersister__) {
            $this->__isInitialized__ = true;
            $class = $this->__documentPersister__->getClassMetadata();
            $original = $this->__documentPersister__->load($this->__identifier__);
            if ($original === null) {
                throw \Doctrine\ODM\MongoDB\MongoDBException::documentNotFound(get_class($this), $this->__identifier__);
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->__documentPersister__, $this->__identifier__);
        }
        
    }
}