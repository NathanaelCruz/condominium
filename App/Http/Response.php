<?php

namespace App\Http;

use App\Config\Config;

class Response
{
    const STATUS_200 = 200;
    const STATUS_500 = 500;

    private $message;
    private $code;
    private $redirect;
    private $pathMain;
    private $data;

    public function __construct()
    {
        $this->message = '';
        $this->code = 0;
    }

    public function setStatus($code, $message, $redirect = '', $pathMain = '', $data = '')
    {
        $this->code = $code;
        $this->message = $message;
        $this->redirect = Config::getHost() . $redirect;
        $this->pathMain = $pathMain;
        $this->data = $data;
    }

    public function resolve()
    {
        header("Content-type: application/json; charset=utf-8");

        echo json_encode(array(
            "code" => $this->code,
            "message" => $this->message,
            "redirect" => $this->redirect,
            "pathMain" => $this->pathMain,
            "data" => $this->data
        ), 1);
    }
}