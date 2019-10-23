<?php

namespace App\Config;

class Config 
{

    CONST HOST_NAME = '127.0.0.1';
    CONST DBNAME = 'RESIDENTIAL_CONDOMINIUM_DB';
    CONST LOGINDB = '';
    CONST PASSDB = '';
    CONST EMAIL = '';
    CONST EMAIL_PASS = '';
    CONST HOST_SEND = 'smtp.gmail.com';
    CONST PORT_SEND = 587;
    CONST PROTOCOL_SECURE_SEND = 'tls';
    CONST NAME_FROM_SEND = 'Condomínio Millenium';
    CONST TITLE_EMAIL = 'Condominium Millenium';

    public static function getHost()
    {

        return 'http://localhost/condominium/';

    }

    public function getDirAbsolute ()
    {

        return $_SERVER['DOCUMENT_ROOT'] . '/condominium';

    }

    public function unlinkRecursive($dir, $deleteRootToo) 
    { 
        if(!$dh = @opendir($dir)) 
        { 
            return; 
        } 
        while (false !== ($obj = readdir($dh))) 
        { 
            if($obj == '.' || $obj == '..') 
            { 
                continue; 
            } 
    
            if (!@unlink($dir . '/' . $obj)) 
            { 
                unlinkRecursive($dir.'/'.$obj, true); 
            } 
        } 
        closedir($dh); 
        if ($deleteRootToo) 
        { 
            @rmdir($dir); 
        } 
        return; 
    } 


}