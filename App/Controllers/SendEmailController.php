<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use App\Config\Config;

class SendEmailController
{

    private $configInstance;

    public function send ($data, $token) {

        $this->configInstance = new Config();

        $mail = new PHPMailer();
        $mail->isSMTP();
		$mail->SMTPOptions = array(
		    'ssl' => array(
		        'verify_peer' => false,
		        'verify_peer_name' => false,
		        'allow_self_signed' => true

		    )
        );
		$mail->SMTPDebug = 0;
        $mail->Host = Config::HOST_SEND;
        $mail->Port = Config::PORT_SEND;

        $mail->SMTPSecure = Config::PROTOCOL_SECURE_SEND;
        $mail->SMTPAuth = true;
        $mail->Username = Config::EMAIL;
        $mail->Password = Config::EMAIL_PASS;
        
        $mail->setFrom(Config::EMAIL, Config::NAME_FROM_SEND);
        $mail->addAddress($data['0']['email'], $data['0']['name']);

        $mail->Subject = Config::TITLE_EMAIL;
        $mail->CharSet = 'UTF-8';
        $mail->AddEmbeddedImage($this->configInstance->getDirAbsolute(). '/Storage/site/email/emailIcon.png','emailIcon','emailIcon');
        $mail->AddEmbeddedImage($this->configInstance->getDirAbsolute(). '/Storage/site/email/locked.png', 'locked', 'locked');

        $html = $this->formatedEmail($data['0'], $token);

        $mail->msgHTML($html);
        $mail->AltBody = 'Esqueceu sua senha? É aqui mesmo!';
        
        if (!$mail->send()) {
            
            throw new \Exception("Erro interno 500"); //Erro ao enviar email, algum deles não foram encontrados
        }

    }

    public function formatedEmail ($data, $token)
    {

        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
          <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Condominium Emperium | Esqueci a minha senha!</title>
                <style type="text/css">
                body {margin: 0; padding: 0; min-width: 100%!important;font-family: "Trebuchet MS";}
                .content {width: 100%; max-width: 600px;}
                .header {padding: 40px 30px 20px 30px;}
                body[yahoo] .class {}
                .col425 {width: 425px!important;}
                .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
                .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
                .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
                .innerpadding {padding: 30px 30px 30px 30px;}
                .borderbottom {border-bottom: 1px solid #f2eeed;}
                .h2 {padding: 0 0 15px 0; font-size: 24px; line-height: 28px; font-weight: bold;}
                .bodycopy {font-size: 16px; line-height: 22px;}
                .button {text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px;}
                .button a {color: #ffffff; text-decoration: none;}
                .footer {padding: 20px 30px 15px 30px;}
                .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
                .footercopy a {color: #ffffff; text-decoration: underline;}
                @media only screen and (min-device-width: 601px) {
                    .content {width: 600px !important;}
                    .col425 {width: 425px!important;}
                    .col380 {width: 380px!important;}
                }
                </style>
            </head>
            <body yahoo bgcolor="#f6f8f1">
                <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <table class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="header" bgcolor="#38ada9">
                                        <table width="70" align="left" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="70" style="padding: 0 20px 20px 0;">
                                                    <img src="cid:emailIcon" width="70" height="70" border="0" alt="" / >
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;" style="width: 100%; max-width: 425px;">
                                            <tr>
                                                <td height="70">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td class="subhead" style="padding: 0 0 0 3px;">
                                                                Senha!
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="h1" style="padding: 5px 0 0 0;">
                                                                Esqueceu ela?
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="innerpadding borderbottom">
                            <table class="content" width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="h2">
                                        Bem-vindo ao esqueci minha senha!
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bodycopy">
                                        Através do link neste e-mail você poderá redefinir sua senha. No momento que acessar o link, ele será ativado e a url única contida nele, não poderá mais ser utilizada. Caso isso aconteça, solicite um novo reset de senha ;)
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="innerpadding borderbottom">
        
                            <table class="content" align="center">
                            
                                <tr>
                                    <td>
                                    
                                        <table width="115" align="left" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td height="115" style="padding: 0 20px 20px 0;">
                                                    <img src="cid:locked" width="115" height="115" border="0" alt="" />
                                                </td>
                                            </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="380" align="left" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                            <td>
                                        <![endif]-->
                                        <table class="col380" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 380px;">
                                        <tr>
                                            <td>
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                <td class="bodycopy">
                                                    Acesse esse <a href="';
        $html .= Config::getHost(). 'reset/password/' . $token . '">link</a> , ou clique no botão abaixo.
                                                </td>
                                                </tr>
                                                <tr>
                                                <td style="padding: 20px 0 0 0;">
                                                    <table class="buttonwrapper" bgcolor="#e05443" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="button" height="45">
                                                        <a href="';
        $html .= Config::getHost(). 'reset/password/';
        $html .= $token . '">Acessar</a>
                                                        </td>
                                                    </tr>
                                                    </table>
                                                </td>
                                                </tr>
                                            </table>
                                            </td>
                                        </tr>
                                        </table>
                                        <!--[if (gte mso 9)|(IE)]>
                                            </td>
                                            </tr>
                                        </table>
                                        <![endif]-->
        
                                    </td>
                                </tr>
                            
                            </table>
                            
                        </td>
                    </tr>
        
                    <tr>
                        <td class="footer" bgcolor="#44525f">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" class="footercopy">
                                        &reg; Condominium Emperium, desde 2019<br/>
                                        <a href="' . Config::getHost() . '"><font color="#ffffff">Acesse o site</font></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>';

        return $html;

    }

}