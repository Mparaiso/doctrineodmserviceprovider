<?php

namespace Mparaiso\Doctrine\ORM;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Exception;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineManagerRegistry implements ManagerRegistry{

    protected $managers;
    protected $connections;
    protected $name;
    function __construct($name,array $connections,array $managers,$defaultConnection="default",$defaultManager="default"){
        $this->name = $name;
        $this->managers = $managers;
        $this->connections = $connections;
        $this->defaultManager = $defaultManager;
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * {@inheritDoc}
     */
    function getDefaultConnectionName()
    {
        return $this->defaultConnection;
    }

    /**
     * {@inheritDoc}
     */
    function getConnection($name = NULL)
    {
        if($name==null)$name=$this->getDefaultConnectionName();
        return $this->connections[$name];
    }

    /**
     * {@inheritDoc}
     */
    function getConnections()
    {
        return $this->connections;
    }

    /**
     * {@inheritDoc}
     */
    function getConnectionNames()
    {
        array_keys($this->connections);
    }

    /**
     * {@inheritDoc}
     */
    function getDefaultManagerName()
    {
        return $this->defaultManager;

    }

    /**
     * {@inheritDoc}
     */
    function getManager($name = NULL)
    {
       if($name==null)$name= $this->getDefaultManagerName();
       return $this->managers[$name];
    }

    /**
     * {@inheritDoc}
     */
    function getManagers()
    {
        return $this->managers;
    }

    /**
     * {@inheritDoc}
     */
    function resetManager($name = NULL)
    {
        #@TODO fix it
        throw new Exception("not implemented yet");
    }

    /**
     * {@inheritDoc}
     */
    function getAliasNamespace($alias)
    {
        #@TODO fix it
        throw new Exception("not implemented yet");
    }

    /**
     * {@inheritDoc}
     */
    function getManagerNames()
    {
        return array_keys($this->managers);
    }

    /**
     * {@inheritDoc}
     */
    function getRepository($persistentObject, $persistentManagerName = NULL)
    {
        $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * {@inheritDoc}
     */
    function getManagerForClass($class)
    {
        foreach($this->managers as $manager){
            /* @var $manager \Doctrine\ORM\EntityManager */
            if(!$manager->getMetadataFactory()->isTransient($class)){
                return $manager;
            }
        }
        return null;
    }
}
