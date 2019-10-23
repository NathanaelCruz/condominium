<?php

require_once('vendor/autoload.php');

use App\Config\Connection;
use App\Config\Config;

$app = new Slim\App(
    [

        'settings' => [

            'displayErrorDetails' => true

        ]

    ]
);

$container = $app->getContainer();

$container['view'] = function ($container)
{

    $view = new \Slim\Views\Twig(__DIR__ . '/public/Views', [

        'cache' => false

    ]);

    $view->addExtension(

        new \Slim\Views\TwigExtension(

            $container->router, $container->request->getUri()

        )

    );

    return $view;

};

include 'App/Config/ControllerList.php';

session_start();

$app->get('/', 'UserController:index')->setName('HomeUser');

$app->get('/forgot/password',
'UserController:iForgotMyPassword')->setName('ForgotPasswordUser');

$app->get('/reset/password/{codeForReset}',
'UserController:resetMyPassword')->setName('ResetPasswordUser');

$app->get('/area/{functionAccess}',
'UserController:homeAccessUser')->setName('AreaUser');

$app->get('/area/{functionAccess}/myaccount/{tokenUser}',
'UserController:myAccountUserShow')->setName('MyAccountUser');

$app->get('/area/{functionAccess}/list/{areaChoice}[/{page}]',
'UserController:listItens')->setName('ListObjects');

$app->get('/area/{functionAccess}/show/{areaChoice}/{idItem}',
'UserController:showItens')->setName('ShowObjects');

$app->get('/area/{functionAccess}/create/{areaChoice}',
'UserController:createItem')->setName('CreateObjects');

$app->get('/area/{functionAccess}/update/{areaChoice}/{idItem}[/{idUser}]',
'UserController:updateItem')->setName('UpdateObjects');

$app->get('/area/{functionAccess}/search/EE/exit',
'UserController:recordExitCondominium')->setName('UpdateExitObjects');

$app->get('/area/{functionAccess}/exit/for/update/{idItem}',
'UserController:recordExitCondominiumForUpdate')->setName('UpdateExitEE');

$app->get('/area/{functionAccess}/report',
'UserController:reportRequest')->setName('ReportRequest');

$app->post('/login/request',
'UserController:consultLoginRequest')->setName('LoginUser');

$app->post('/destroyer/login/request',
'UserController:destroyerLoginRequest')->setName('logoutUser');

$app->post('/update/MyAccount',
'UserController:updateMyAccountInfo')->setName('UpdateMyAccountUser');

$app->any('/update/MyAccount/new/photo/{token}',
'UserController:updateAccountNewPhoto')->setName('UpdateMyPhotoAccountUser');

$app->post('/update/MyAccount/run/photoforprofile',
'UserController:updateMyPhotoForProfile')->setName('UpdateForProfilePhoto');

$app->post('/update/MyAccount/for/residents',
'UserController:updateUserForResidents')->setName('UpdateForResidents');

$app->any('/create/photo/temp/{photoTemp}',
'UserController:updateAccountNewPhoto')->setName('StoragePhotoForTemporary');

$app->any('/storage/new/{typeRegister}',
'UserController:storageNewItem')->setName('StorageNewPerson');

$app->post('/storage/new/EE/Entrance',
'UserController:storageRecordEE')->setName('StorageNewEE');

$app->any('/update/new/info/{areaChoice}/{idItem}[/{idUser}]',
'UserController:updateItemNewInfo')->setName('UpdatePerson');

$app->any('/update/new/photo/foruser/photoprofile/{areaChoice}',
'UserController:updateNewPhotoForUser')->setName('UpdatePhotoPerson');

$app->any('/search/user/forvisit',
'UserController:searchVisitForEE')->setName('SearchForEE');

$app->any('/search/user/exit/validate',
'UserController:searchVisitForExit')->setName('SearchForEE');

$app->post('/storage/new/EE/Exit',
'UserController:storageRecordExit')->setName('StorageNewExit');

$app->post('/deleted/item',
'UserController:deletedItemNow')->setName('DeletedItem');

$app->get('/report/export/forUser',
'UserController:reportExport')->setName('ExportReport');

$app->post('/forgot/password/send',
'UserController:sendEmailForgot')->setName('ForgotPasswordUserSend');

$app->post('/forgot/update/password/send/for/forgot',
'UserController:sendEmailForgotUpdate')->setName('ForgotPasswordUserUpdate');


$app->run();