<?php

namespace App\Controllers;

use App\Http\Response;

class Controller
{

    protected $container;
    protected $response;
    private $data = [];

    public function __construct (\Slim\Container $container)
    {

        $this->container = $container;
        $this->response = new Response();

    }

    public function __get ($key)
    {

        if ($this->container->{$key}) 
        {

            return $this->container->{$key};

        }

    }

    public function __call ($name, $arguments)
    {

        $method = substr($name, 0, 3);
        $nameField = substr($name, 3, strlen($name));

        switch ($method) {

            case 'set':
                $this->data[$nameField] = $arguments[0];
            break;
            
            case 'get':
                return isset($this->data[$nameField]) ? $this->data[$nameField] : null;
            break;

        }

    }

    public function setData ($data = array())
    {

        foreach ($data as $key => $value) {
            
            $this->{'set' . $key}($value);

        }

    }

    public function getData ()
    {

        return $this->data;

    }


}
