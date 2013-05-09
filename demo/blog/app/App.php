<?php
use Silex\Application;


class App extends Application
{
    function __construct(array $values = array())
    {
        parent::__construct($values);
        $config = new Config;
        $this->register($config);
        $this->mount("/",$config);

    }

}
