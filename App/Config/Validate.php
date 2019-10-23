<?php

namespace App\Config;

use App\Models\UserModel;

class Validate
{

    private $conn;
    private $userModelUtility;

    public static function fieldRequired ($fieldForValidate, 
    $message = 'Erro interno')
    {

        if (!preg_match('/[\d\w]{1,}/', trim($fieldForValidate))) {

            throw new \Exception($message);

        }

    }

    public static function fieldMaximumRequired ($fieldForValidate,
    $fieldMaximum, 
    $message = 'Erro interno')
    {

        if (strlen(trim($fieldForValidate)) > $fieldMaximum) {

            throw new \Exception($message . $fieldMaximum . ' caracteres');

        }

    }


    public static function fieldMinimumRequired ($fieldForValidate, 
        $fieldMinimum, 
        $message = 'Erro interno'
        ) 
    {

        if (strlen(trim($fieldForValidate)) < $fieldMinimum) {

            throw new \Exception($message . $fieldMinimum . ' caracteres');

        }

    }


    public static function notContentWhiteSpaceInField (
        $fieldForValidate, 
        $message = 'Erro interno')
    {

        if (strpos($fieldForValidate," ") !== false) {

            throw new \Exception($message);

        }

    }

    public static function emailFormatValidate ($emailForValidate, 
    $message = 'Erro interno')
    {

        if (!preg_match('/[a-zA-Zà-úÀ-Ú0-9._%+-]+@[a-zA-Zà-úÀ-Ú0-9.-]+\.[a-z]{2,3}/', trim($emailForValidate))) {

            throw new \Exception($message);

        }

    }

    public function notContentLoginEqualInDB ($fieldForValidate,
    $message = 'Erro interno')
    {

        $this->userModelUtility = new UserModel();
        
        $result = $this->userModelUtility;
        $countLogins = $result->countUserWorker($fieldForValidate);

        if ( intval($countLogins) != 0) {

            throw new \Exception($message);

        }

    }

    public static function cpfFormatValidate ($fieldForValidate, 
    $message = 'Erro interno')
    {
        if (!preg_match('/[0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2}$/', trim($fieldForValidate))) {

            throw new \Exception($message);

        }

    }


    public function cpfIsValidValue ($fieldForValidate, 
    $message = 'Erro interno') {
        
        $this->cpfValidate = preg_replace( '/[^0-9]/is', '', $fieldForValidate );

        $this->newCpf = $this->validateDigitsCpf(substr($this->cpfValidate, 0, 9));

        $this->newCpf = $this->validateDigitsCpf($this->newCpf, 11);

        if ($this->cpfValidate !== $this->newCpf) {

            throw new \Exception($message);

        }
        
    }

    public function validateDigitsCpf ($digits,
        $sequenceValidateCpf = 10,
        $sumDigits = 0)
    {

        for ( $i = 0; $i < strlen( $digits ); $i++  ) {

            $sumDigits = $sumDigits + ( $digits[$i] * $sequenceValidateCpf );

            $sequenceValidateCpf--;

        }

        $sumDigits = $sumDigits % 11;

        if ( $sumDigits < 2 ) {
            
            $sumDigits = 0;

        } else {

            $sumDigits = 11 - $sumDigits;

        }

        $cpf = $digits . $sumDigits;

        return $cpf;

    }

    public static function lettersAndNumbersOnly ($fieldForValidate,
    $message = 'Erro interno')
    {

        $alphabetForValidate =  array("á","à","ã","â","ä","é","è","ê","ë","í","ì","î","ï","ó","ò","õ","ô","ö","ú","ù","û","ü","ç","Á","À","Ã","Â","Ä","É","È","Ê","Ë","Í","Ì","Î","Ï","Ó","Ò","Õ","Ô","Ö","Ú","Ù","Û","Ü","Ç");

        $specialChar = array("!", "#", "$", "%", "\"", "&", "*", "(", ")", "-", "_", "'", "`", "^", "~", ":", ";", ">", ".", ",", "<", "/", "?", "]", "}", "[", "{", "=", "+");

        foreach ($specialChar as $key) {

            array_push($alphabetForValidate, $key);

        }
       
        for ($a=0; $a < count($alphabetForValidate); $a++) { 
    
           if (strpos($fieldForValidate, $alphabetForValidate[$a]) !== false) {
    
                throw new \Exception($message);
                
            }
    
        }

    }


}