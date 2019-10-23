<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Config\Config;
use App\Config\Validate;
use App\Models\UserModel;
use App\Controllers\SendEmailController;
use App\Http\Response;

class UserController extends Controller
{

    private $search;
    private $viewShow;
    private $visits;
    private $residents;
    private $visitCurrent;
    private $workers;
    private $countNumberHouse;
    private $numberFormated;
    private $dataForm;
    private $functionAccess;
    private $userModelUtility;
    private $myAccount;
    private $areaUtility;
    private $fileUploadPhoto;
    private $fileUploadPhotoObject;
    private $configInstance;
    private $isAVisit;
    private $userPhotoTemp;
    private $start;
    private $totalPages;
    private $dataItens;
    private $totalRegisterShow;
    private $countEEExit;
    private $avgIncome;
    const SESSION = 'USER_CONDOMINIUM_SECRET';

    #INDEX: login do sistema
    public function index ($request, $response, $args)
    {
        return $this->view->render($response, 'index.twig', [
            'appHost' => Config::getHost(),
        ]);

    }
    
    #FORGOT_PAGE: Página para reset de Senha
    public function iForgotMyPassword ($request, $response, $args)
    {
        return $this->view->render($response, 'forgotPassword.twig', [
            'appHost' => Config::getHost()
        ]);

    }
    
    #RESET_PASS_PAGE: Página para resetar a senha
    public function resetMyPassword ($request, $response, $args)
    {

        $this->userModelUtility = new userModel();

        $requestToken = $this->userModelUtility->validateRequestToken($args['codeForReset']);
        $diff = $this->difTimeForgot($requestToken['data']['0']['request_forgot_date']);

        $error = '';
        $message = '';
        $nowDate = date("Y-m-d H:i:s");

        if ($requestToken['count'] <= 0) {

            $message = 'Esse código é inválido.';
            $error = 'NA';

        }

        if ($requestToken['data']['0']['status_forgot_pass'] == 0 || 
            $diff >= 1) {

            $message = 'Esse código é Expirado ou Utilizado.';
            $error = 'NA';

        }

        return $this->view->render($response, 'resetPassword.twig', [
            'appHost' => Config::getHost(),
            'codeForReset' => $args['codeForReset'],
            'error' => $error,
            'msgError' => $message
        ]);

    }
    
    #HOME_ADMIN: Página inicial do painel administrador
    public function homeAccessUser ($request, $response, $args)
    {

        $this->verifyUserLogin();
        $this->userModelUtility = new userModel();

        $this->visits = $this->formatNumberInShort(
            $this->userModelUtility->countVisitsCurrent()
        );

        $this->residents = $this->formatNumberInShort(
            $this->userModelUtility->countResidentsCurrent()
        );

        $this->visitCurrent = $this->formatNumberInShort(
            $this->userModelUtility->countEECurrent()
        );

        $this->workers = $this->formatNumberInShort(
            $this->userModelUtility->countWorkersNotAdmin()
        );


        return $this->view->render($response, 'Admin/home.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => 'home',
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'dateToday' => [
                'day' => date("d"),
                'month' => date("m")
            ],
            'visits' => $this->visits,
            'residents' => $this->residents,
            'visitCurrent' => $this->visitCurrent,
            'workers' => $this->workers,
            'tokenUser' => $_SESSION[self::SESSION]['token_user']
        ]);

    }
    
    #ACCOUNT_PAGE: Mostra formulário da conta logada no momento
    public function myAccountUserShow ($request, $response, $args)
    {

        $this->verifyUserLogin();
        $this->userModelUtility = new userModel();
        $this->myAccount = $this->userModelUtility->findUserForAccount(
            $_SESSION[self::SESSION]['token_user']
        );

        $this->isAVisit = $this->userModelUtility->consultIsAVisit(
            $_SESSION[self::SESSION]['id_user']
        );

        $this->areaUtility = 'account';
        
        return $this->view->render($response, 'Admin/frmMyAccount.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'area' => $this->areaUtility,
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'isAVisit' => $this->isAVisit,
            'dataConsult' => [
                'user' => $this->myAccount,
                'count' => count($this->myAccount)
            ],
            'tokenUser' => $_SESSION[self::SESSION]['token_user']
        ]);

    }
    
    #LIST: Mostra os itens solicitados
    public function listItens ($request, $response, $args)
    {
        $this->verifyUserLogin();
        $this->search = ($request->getParam('search') !== null) ? $request->getParam('search') : '.';
        
        $this->totalRegisterShow = 10;
        $this->start = intval((isset($args['page']) ? $args['page'] : 0));
        $this->userModelUtility = new UserModel();
        
        if ($args['areaChoice'] == 'worker') {
            
            
            $this->dataItens = $this->userModelUtility->listItemWorker(
                $this->start, 
                $this->totalRegisterShow,
                $this->search
            );
            
            $this->totalPages = ceil(
                $this->userModelUtility->countWorkersNotAdmin() / $this->totalRegisterShow
            );

        } elseif ($args['areaChoice'] == 'visit') {

            $this->dataItens = $this->userModelUtility->listItemVisitors(
                $this->start, 
                $this->totalRegisterShow,
                $this->search
            );
            
            $this->totalPages = ceil(
                $this->userModelUtility->countVisitsCurrent() / $this->totalRegisterShow
            );

        } elseif ($args['areaChoice'] == 'resident') {

            $this->dataItens = $this->userModelUtility->listItemResidents(
                $this->start, 
                $this->totalRegisterShow,
                $this->search
            );
            
            $this->totalPages = ceil(
                $this->userModelUtility->countResidentsCurrent() / $this->totalRegisterShow
            );

        } elseif ($args['areaChoice'] == 'EE') {


            $this->dataItens = $this->userModelUtility->listItemEE(
                $this->start, 
                $this->totalRegisterShow,
                $this->search
            );
            
            $this->totalPages = ceil(
                $this->userModelUtility->countEECurrent() / $this->totalRegisterShow
            );

        } else {

            $this->dataItens = [];
            $this->totalPages = 0;

        }
        

        return $this->view->render($response, 'Admin/list.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => $args['areaChoice'],
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'tokenUser' => $_SESSION[self::SESSION]['token_user'],
            'dataItens' => $this->dataItens,
            'totalPages' => $this->totalPages,
            'search' => ($this->search != null) ? $this->search : '',
            'page' => isset($args['page']) ? $args['page'] : 1
        ]);

    }

    #SHOW: Mostra os itens solicitados
    public function showItens ($request, $response, $args)
    {
        
        $this->verifyUserLogin();
        $this->userModelUtility = new UserModel();

        if ($args['areaChoice'] == 'EE') {

                $this->dataItens = $this->userModelUtility->findEEDB(
                $args['idItem']
            );

            $this->viewShow = 'Admin/ETOE/showETOE.twig';

        } else {

            if ($args['areaChoice'] == 'worker') {

                $this->dataItens = $this->userModelUtility->findWorkerDB(
                    $args['idItem']
                );

            } elseif ($args['areaChoice'] == 'visit') {

                $this->dataItens = $this->userModelUtility->findVisitDB(
                    $args['idItem']
                );

            } else {

                $this->dataItens = $this->userModelUtility->findResidentDB(
                    $args['idItem']
                );

            }

            $this->viewShow = 'Admin/showUsers.twig';

        }

        return $this->view->render($response, $this->viewShow, [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => $args['areaChoice'],
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'tokenUser' => $_SESSION[self::SESSION]['token_user'],
            'idItem' => $args['idItem'],
            'dataConsult' => ($this->dataItens != null) ? $this->dataItens : 'NA'
        ]);

    }
    
    #CREATE: Página para criação de Item
    public function createItem ($request, $response, $args)
    {
        $this->verifyUserLogin();
        if ($args['areaChoice'] == 'EE') {

            $this->viewShow = 'Admin/ETOE/frmCreateEE.twig';
            $this->areaUtility = null;

        } else {

            $this->viewShow = 'Admin/frmCreate.twig';
            $this->areaUtility = 'createUser';

        }

        $this->userPhotoTemp = rand(1000000, 999999);

        return $this->view->render($response, $this->viewShow, [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => $args['areaChoice'],
            'photoTemp' => md5($this->userPhotoTemp),
            'area' => $this->areaUtility,
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'tokenUser' => $_SESSION[self::SESSION]['token_user']
        ]);

    }
    
    #UPDATE_PAGE: Página para atualização de Item
    public function updateItem ($request, $response, $args)
    {

        $this->verifyUserLogin();
        $this->userModelUtility = new UserModel();

        if ($args['areaChoice'] == 'worker') {

            $this->dataItens = $this->userModelUtility->findWorkerDB(
                $args['idItem']
            );

        } elseif ($args['areaChoice'] == 'visit') {

            $this->dataItens = $this->userModelUtility->findVisitDB(
                $args['idItem']
            );

        } else {

            $this->dataItens = $this->userModelUtility->findResidentDB(
                $args['idItem']
            );

        }

        $this->areaUtility = 'updateUser';

        return $this->view->render($response, 'Admin/frmUpdate.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => $args['areaChoice'],
            'idItem' => $args['idItem'],
            'idUser' => $args['idUser'],
            'data' => $_SESSION[self::SESSION],
            'area' => $this->areaUtility,
            'tokenUser' => $_SESSION[self::SESSION]['token_user'],
            'dataConsult' => $this->dataItens
        ]);

    }
    
    #RECORD_EXIT: Formulário de Entrada
    public function recordExitCondominium ($request, $response, $args)
    {

        $this->verifyUserLogin();

        return $this->view->render($response, 'Admin/ETOE/frmSearchEE.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => 'EE',
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'tokenUser' => $_SESSION[self::SESSION]['token_user']
        ]);

    }

    #RECORD_EXIT_UPDATE: Formulário de Saída
    public function recordExitCondominiumForUpdate ($request, $response, $args)
    {

        $this->verifyUserLogin();
        $this->userModelUtility = new UserModel();

        $dataConsult = $this->userModelUtility->findEEDB(
            $args['idItem']
        );

        return $this->view->render($response, 'Admin/ETOE/frmUpdateEE.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'areaChoice' => 'EE',
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'tokenUser' => $_SESSION[self::SESSION]['token_user'],
            'dataConsult' => $dataConsult['user']
        ]);

    }

    #UPDATE: Atualização de Item solicitado
    public function updateItemNewInfo ($request, $response, $args)
    {


        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();
        $validateForValidate = new Validate();

        try{

            Validate::fieldRequired($this->getname(), "Campo Nome é requerido.");
            Validate::fieldRequired($this->getfldEmail(), "Campo Email é requerido.");
            Validate::fieldRequired($this->getrg(), "Campo RG é requerido.");
            Validate::fieldRequired($this->getcpf(), "Campo CPF é requerido.");
            Validate::fieldRequired($this->getphone(), "Campo Telefone é requerido.");
            Validate::fieldRequired($this->getbirthday(), "Campo Data de nascimento é requerido.");

            Validate::fieldMaximumRequired($this->getname(), 200, "O nome pode ter no máximo");
            Validate::fieldMaximumRequired($this->getfldEmail(), 255, "O e-mail pode ter no máximo");
            Validate::fieldMaximumRequired($this->getrg(), 12, "O RG pode ter no máximo");
            Validate::fieldMaximumRequired($this->getcpf(), 14, "O CPF pode ter no máximo");
            Validate::fieldMaximumRequired($this->getphone(), 20, "O Telefone pode ter no máximo");

            #Validar o formato de email
            Validate::emailFormatValidate(
                $this->getfldEmail(), 
                "Campo E-mail inválido."
            );

            #Validar formato e se o CPF é valido
            Validate::cpfFormatValidate(
                $this->getcpf(), 
                "Formato inválido de CPF."
            );


            $validateForValidate->cpfIsValidValue(
                $this->getcpf(), 
                "CPF inválido."
            );

            $this->userModelUtility->updateItemUser(
                $this->getData(),
                $args['idUser']
            );
            
            $countEmail = $this->userModelUtility->countUserEmail(
                $this->getfldEmail()
            );

            if ($countEmail > 1) {

                throw new \Exception("Email em uso.");

            }

            if ($args['areaChoice'] == 'worker') {

                Validate::fieldRequired($this->getfldLogin(), "Campo Login é requerido.");
                Validate::fieldMaximumRequired($this->getfldLogin(), 200, "O login pode ter no máximo");

                Validate::notContentWhiteSpaceInField(
                    $this->getfldLogin(), 
                    "Campo Login não pode ser em branco."
                );

                #Somente letras e numeros
                Validate::lettersAndNumbersOnly(
                    $this->getfldLogin(), 
                    "Campo Login deve conter somente números e letras."
                );

                $countWorker = $this->userModelUtility->countUserWorker(
                    $this->getfldLogin()
                );

                if ($countWorker > 1) {

                    throw new \Exception("Login Em uso.");

                } else {

                    if ($this->getfldPassword() == null ||
                     $this->getfldPassword() == '') {
                        
                        $this->userModelUtility->updateItemWorker(
                            $this->getData(),
                            $args['idItem']
                        );

                    } else {

                        Validate::fieldMinimumRequired(
                            $this->getfldPassword(),
                            5, 
                            "Campo Senha deve ter no mínimo ."
                        );

                        Validate::fieldMaximumRequired(
                            $this->getfldPassword(),
                            200, 
                            "Campo Senha deve ter no mínimo ."
                        );

                        Validate::notContentWhiteSpaceInField(
                            $this->getfldPassword(), 
                            "Campo Senha não pode ser em branco."
                        );

                        $this->userModelUtility->updateItemWorker(
                            $this->getData(),
                            $args['idItem'],
                            1                        
                        );

                    }

                }

            } else {

                Validate::fieldRequired($this->getavgIncome(), "Campo Média Salarial é requerido.");
                Validate::fieldMaximumRequired($this->getavgIncome(), 10, "A média salarial pode ter no máximo");

                $this->userModelUtility->updateItemResidents(
                    $this->getData(),
                    $args['idItem']         
                );

            }

            $findUser = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Alterado com sucesso!", "area/" . strval($this->functionAccess) . "/list/" . $args['areaChoice'],
            Config::getHost()
                );
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }
    
    #REPORT_PAGE: Página de relatórios do sistema
    public function reportRequest ($request, $response, $args)
    {

        $this->verifyUserLogin();
        return $this->view->render($response, 'Admin/report.twig', [
            'appHost' => Config::getHost(),
            'functionAccess' => $args['functionAccess'],
            'data' => $_SESSION[self::SESSION],
            'access' => $_SESSION[self::SESSION]['profile'],
            'tokenUser' => $_SESSION[self::SESSION]['token_user']
        ]);

    }


    #LOGIN: Valida a possibilidade de logar
    public function consultLoginRequest ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        try{

            Validate::fieldRequired($this->getlogin(), "Campo Login é requerido.");
            Validate::fieldRequired($this->getpassword(), "Campo Senha é requerido.");

            Validate::fieldMaximumRequired($this->getlogin(), 150, "O login pode ter no máximo");
            Validate::fieldMaximumRequired($this->getpassword(), 70, "A senha pode ter no máximo");

            Validate::notContentWhiteSpaceInField($this->getlogin(), "O login está em branco.");
            Validate::notContentWhiteSpaceInField($this->getpassword(), "A senha está em branco.");

            $this->userModelUtility = new UserModel();

            $countWorker = $this->userModelUtility->countUserWorker(
                $this->getlogin()
            );

            if ($countWorker <= 0) {

                throw new \Exception("Login e/ou senha inválidos.");

            } else {

                $findUser = $this->userModelUtility->findUserWorker(
                    $this->getlogin()
                );

                if (password_verify($this->getpassword(), 
                    $findUser[0]['password']) == true) {
                    
                    $this->setData($findUser[0]);

                    $_SESSION[self::SESSION] = $this->getData();

                    if ($findUser[0]['profile'] == '0') {

                        $this->functionAccess = 'admin';

                    } else {

                        $this->functionAccess = 'worker';

                    }

                } else {

                    $this->response->setStatus(Response::STATUS_500, "Usuário e/ou senha inválida.", "","");

                }
                

                $this->response->setStatus(Response::STATUS_200, "Login com sucesso!", "area/" . strval($this->functionAccess),
                Config::getHost()
                );
            }
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #LOGOUT: Sai da sessão
    public function destroyerLoginRequest ()
    {

        try{
 
            $_SESSION[UserController::SESSION] = NULL;

            $this->response->setStatus(Response::STATUS_200, "Até mais ;)", "", 
            Config::getHost());
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, "Não foi possível deslogar :(");
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #UPDATE_MY_ACCOUNT: Atualiza informações da conta
    public function updateMyAccountInfo ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        try{


            #Campo requerido
            Validate::fieldRequired($this->getname(), "Campo Nome é requerido.");
            Validate::fieldRequired($this->getfldEmail(), "Campo E-mail é requerido.");
            Validate::fieldRequired($this->getfldLogin(), "Campo Login é requerido.");
            Validate::fieldRequired($this->getrg(), "Campo RG é requerido.");
            Validate::fieldRequired($this->getcpf(), "Campo CPF é requerido.");
            Validate::fieldRequired($this->getphone(), "Campo telefone é requerido.");
            Validate::fieldRequired($this->getbirthday(), "Campo Data de nascimento é requerido.");


            #Validar tamanho máximo do campo
            Validate::fieldMaximumRequired(
                $this->getname(), 
                200, 
                "Campo Nome deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getfldEmail(), 
                255, 
                "Campo E-mail deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getfldLogin(), 
                250, 
                "Campo Login deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getrg(), 
                12, 
                "Campo RG deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getphone(), 
                20, 
                "Campo Telefone deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getcpf(), 
                14, 
                "Campo CPF deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getbirthday(), 
                20, 
                "Campo Data de nascimento deve ter no máximo "
            );


            #Se contem espaço em branco
            Validate::notContentWhiteSpaceInField(
                $this->getfldLogin(), 
                "Campo Login não pode ser em branco."
            );


            #Somente letras e numeros
            Validate::lettersAndNumbersOnly(
                $this->getfldLogin(), 
                "Campo Login deve conter somente números e letras."
            );
            

            #Validar o formato de email
            Validate::emailFormatValidate(
                $this->getfldEmail(), 
                "Campo E-mail inválido."
            );

            $countEmail = $this->userModelUtility->countUserEmail(
                $this->getfldEmail()
            );

            if ($countEmail > 1) {

                throw new \Exception("Email em uso.");

            }

            $validateForValidate = new Validate();

            $findUserWorker = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );

            if ($findUserWorker[0]['login'] != $this->getfldLogin()) {

                #Validar se há algum login igual ao digitado
                $validateForValidate->notContentLoginEqualInDB(
                    $this->getfldLogin(),
                    "Campo Login já existente."
                );

            }

            #Validar formato e se o CPF é valido
            Validate::cpfFormatValidate(
                $this->getcpf(), 
                "Formato inválido de CPF."
            );

            $validateForValidate->cpfIsValidValue(
                $this->getcpf(), 
                "CPF inválido."
            );

            #Valida se está tentando mudar a senha ou não
            if ($this->getfldPassword() != null || 
            $this->getfldPassword() != '') {

                Validate::fieldMinimumRequired(
                    $this->getfldPassword(),
                    5, 
                    "Campo Senha deve ter no mínimo ."
                );

                Validate::fieldMaximumRequired(
                    $this->getfldPassword(),
                    200, 
                    "Campo Senha deve ter no mínimo ."
                );

                Validate::notContentWhiteSpaceInField(
                    $this->getfldPassword(), 
                    "Campo Senha não pode ser em branco."
                );

                $this->userModelUtility->updateMyAccountDB(
                    $this->getData()
                );

            } else {


                $this->userModelUtility->updateMyAccountDB(
                    $this->getData(),
                    'Not'
                );

            }

            $findUser = $this->userModelUtility->findUserWorker(
                $this->getfldLogin()
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $_SESSION[self::SESSION]['login'] = $this->getfldLogin();

            $this->response->setStatus(Response::STATUS_200, "Alterado com sucesso!", "area/" . strval($this->functionAccess) . "/myaccount/" . $_SESSION[self::SESSION]['token_user'],
            Config::getHost()
                );
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #UPDATE_FOR_RESIDENT: Transforma um funcionário logado em morador
    public function updateUserForResidents ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        try{


            #Campo requerido
            Validate::fieldRequired($this->gettoken_userForResidents(), "Campo token inválido requerido.");
            Validate::fieldRequired($this->getavgIncome(), "Média salarial é requerida.");


            #Validar tamanho máximo do campo
            Validate::fieldMaximumRequired(
                $this->getavgIncome(), 
                10, 
                "Média Salarial deve ter no máximo "
            );

            #Validar o minimo
            Validate::fieldMinimumRequired(
                $this->gettoken_userForResidents(),
                33, 
                "Campo token, inválido."
            );
            Validate::fieldMinimumRequired(
                $this->getavgIncome(),
                6, 
                "Média salarial deve ser maior que ."
            );

            $this->userModelUtility->updateUserForResidentsDB(
                $this->getData()
            );
            
            if ($_SESSION[self::SESSION]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Bem-vindo visinho ;)", "area/" . strval($this->functionAccess) . "/myaccount/" . $_SESSION[self::SESSION]['token_user'],
            Config::getHost()
                );
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #CREATE: Insere um novo item no banco de dados
    public function storageNewItem ($request, $response, $args)
    {

        $this->verifyUserLogin();
        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();
        $validateForValidate = new Validate();

        try{

            #Campo requerido
            Validate::fieldRequired($this->getname(), "Campo Nome é requerido.");
            Validate::fieldRequired($this->getfldEmail(), "Campo E-mail é requerido.");
            Validate::fieldRequired($this->getrg(), "Campo RG é requerido.");
            Validate::fieldRequired($this->getcpf(), "Campo CPF é requerido.");
            Validate::fieldRequired($this->getphone(), "Campo telefone é requerido.");
            Validate::fieldRequired($this->getbirthday(), "Campo Data de nascimento é requerido.");

            #Validar formato e se o CPF é valido
            Validate::cpfFormatValidate(
                $this->getcpf(), 
                "Formato inválido de CPF."
            );

            $validateForValidate->cpfIsValidValue(
                $this->getcpf(), 
                "CPF inválido."
            );

            #Validar tamanho máximo do campo
            Validate::fieldMaximumRequired(
                $this->getname(), 
                200, 
                "Campo Nome deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getfldEmail(), 
                255, 
                "Campo E-mail deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getrg(), 
                12, 
                "Campo RG deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getphone(), 
                20, 
                "Campo Telefone deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getcpf(), 
                14, 
                "Campo CPF deve ter no máximo "
            );

            Validate::fieldMaximumRequired(
                $this->getbirthday(), 
                20, 
                "Campo Data de nascimento deve ter no máximo "
            );

            #Validar o formato de email
            Validate::emailFormatValidate(
                $this->getfldEmail(), 
                "Campo E-mail inválido."
            );

            $countEmail = $this->userModelUtility->countUserEmail(
                $this->getfldEmail()
            );

            if ($countEmail > 1) {

                throw new \Exception("Email em uso.");

            }

            $lastIdInsert = $this->userModelUtility->insertUserInDB($this->getData(), 1);
                
            $token = $lastIdInsert . $this->getphotoTemp();
            $this->userModelUtility->updateTokenUser($lastIdInsert, $token);

            #valida se é worker
            if ((array_key_exists("fldLogin", $this->dataForm) == true) && 
                (array_key_exists("fldPassword", $this->dataForm) == true)) {

                #Se contem espaço em branco
                Validate::notContentWhiteSpaceInField(
                    $this->getfldLogin(), 
                    "Campo Login não pode ser em branco."
                );

                Validate::fieldMaximumRequired(
                    $this->getfldLogin(), 
                    250, 
                    "Campo Login deve ter no máximo "
                );

                #Somente letras e numeros
                Validate::lettersAndNumbersOnly(
                    $this->getfldLogin(), 
                    "Campo Login deve conter somente números e letras."
                );


                $findUserWorker = $this->userModelUtility->findUserWorker(
                    $this->getfldLogin()
                );

                #Validar se há algum login igual ao digitado
                $validateForValidate->notContentLoginEqualInDB(
                    $this->getfldLogin(),
                    "Campo Login já existente."
                );

                #cadastrar como funcionário

                $this->userModelUtility->createWorkerDB(
                    intval($lastIdInsert), 
                    $this->getData()
                );

            } elseif (array_key_exists("avgIncome", $this->dataForm) == true) {#valida se é resident


                Validate::fieldRequired($this->getavgIncome(), "Média salarial é requerida.");

                #Validar tamanho máximo do campo
                Validate::fieldMaximumRequired(
                    $this->getavgIncome(), 
                    10, 
                    "Média Salarial deve ter no máximo "
                );

                Validate::fieldMinimumRequired(
                    $this->getavgIncome(),
                    6, 
                    "Média salarial deve ser maior que ."
                );

                #cadastrar como morador
                $this->userModelUtility->createResidentDB(
                    intval($lastIdInsert), 
                    $this->getData()
                );

            } else {

                #cadastrar como visitante

                $this->userModelUtility->createVisitantDB(
                    intval($lastIdInsert), 
                    $this->getData()
                );

            }

            $this->movePhotoNewPerfil($lastIdInsert, $this->getphotoTemp());


            $findUser = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Cadastrado com sucesso!", "area/" . strval($this->functionAccess) . "/list/" . $args['typeRegister'],
            Config::getHost()
                );
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #BUSCA: Busca o perfil de visitante na Entrace e Exit
    public function searchVisitForEE ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->userModelUtility = new UserModel();

        
        try {

            $documentFormatated = $this->dataForm['documentSearch'];
            $results = $this->userModelUtility->searchDocument($documentFormatated);

            if (intval($results['countUser']) <= 0) {

                $this->response->setStatus(Response::STATUS_500, "Usuário não encontrado!", '', Config::getHost(), ''
                    );

            } else {

                $this->response->setStatus(Response::STATUS_200, "Usuário Encontrado!", '', Config::getHost(),$results['data']
                    );

            }

            if ($this->dataForm['chanceDocument'] != 'opcCFP' && 
                $this->dataForm['chanceDocument'] != 'opcRG') {

                $this->response->setStatus(Response::STATUS_500, "Opção Inválida!", '', Config::getHost(), ''
                );

            }

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #SEARCH_EXIT: Busca o visitante para a saída
    public function searchVisitForExit ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->userModelUtility = new UserModel();

        try {

            $documentFormatated = $this->dataForm['documentSearchExit'];
            $results = $this->userModelUtility->searchDocumentExit($documentFormatated);

            if (intval($results['countUser']) <= 0) {

                $this->response->setStatus(Response::STATUS_500, "Usuário não encontrado!", '', Config::getHost(), ''
                    );

            } else {

                $this->response->setStatus(Response::STATUS_200, "Usuário Encontrado!", '', Config::getHost(),$results['data']
                    );

            }

            if ($this->dataForm['chanceDocument'] != 'opcCFP' && 
                $this->dataForm['chanceDocument'] != 'opcRG') {

                $this->response->setStatus(Response::STATUS_500, "Opção Inválida!", '', Config::getHost(), ''
                );

            }

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #CREATE: Cria o registro de entrada
    public function storageRecordEE ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        try{

            Validate::fieldRequired($this->getidUser(), "Precisa ser pesquisado um Usuário válido.");

            $this->userModelUtility->storageEntranceDB(
                $_SESSION[self::SESSION]['id_user'],
                $this->getData()
            );
        
            $findUser = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Seja Bem-vindo!", "area/" . strval($this->functionAccess) . "/list/EE",
            Config::getHost()
                );

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #UPDATE: Atualiza a entrada dando uma saída
    public function storageRecordExit ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        try{

            Validate::fieldRequired($this->getidEEUpdate(), "Precisa ser pesquisado um Usuário válido.");

            $countEntrance = $this->userModelUtility->findEEDB($this->dataForm['idEEUpdate']);

            if ($countEntrance['user']['0']['data_exit'] != null) {

                $this->userModelUtility->storageExitDB(
                    $_SESSION[self::SESSION]['id_user'],
                    $this->getData(),
                    0
                );

            } else {

                $this->userModelUtility->storageExitDB(
                    $_SESSION[self::SESSION]['id_user'],
                    $this->getData(),
                    1
                );

            }
        
            $findUser = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Até mais!", "area/" . strval($this->functionAccess) . "/list/EE",
            Config::getHost()
                );

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #DELETE: Deleta um item
    public function deletedItemNow ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        $infoData = explode('-', $this->dataForm['deleteIdentify']);

        $id = $infoData[0];
        $area = $infoData[1];

        try {
            
            $this->userModelUtility->deletedItem(
                $id,
                $area
            );

            $findUser = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Deletado com sucesso!", "area/" . strval($this->functionAccess) . "/list/" . $area,
            Config::getHost()
                );

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }

        return $this->response->resolve();
        exit;

    }

    #REPORT_CHANGE: Determina qual método exportar
    #CSV ou PDF
    public function reportExport ($request, $response, $args)
    {

        if ($request->getParam('type') == 'csv') {

            $this->exportedInCSV($request->getParam('table'));


        } elseif ($request->getParam('type') == 'pdf') {

            $this->exportedInPDF();

        } else {

        }

    }

    #CSV_EXPORT: Exporta relatórios em CSV
    public function exportedInCSV ($table)
    {

        $this->userModelUtility = new UserModel();

        $data = array();
        $datanow = date('d-m-YH-i-s');
        $headerCsv = array();
        $dataDiff = '';


        if ($table == 'workers') {
                
            $filename = $table . $datanow . '.csv';

            header( 'Content-type: application/csv' );   
            header( 'Content-Disposition: attachment; filename=' . $filename);   
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Pragma: no-cache');

            $headerCsv = array(
                0 => "Nome",
                1 => "E-mail",
                2 => "Telefone",
                3 => "Data de Nascimento",
                4 => "Login",
                5 => "Perfil",
                6 => "Token"
            );
            $data = $this->userModelUtility->listItemWorkerAll();

            $worker = fopen( 'php://output', 'w' );
            fputcsv( $worker, $headerCsv );

            foreach ( $data['data'] as $result ) 
            {

                $row = array(
                    0 => $result['name'],
                    1 => $result['email'],
                    2 => $result['phone'],
                    3 => $result['birthday'],
                    4 => $result['login'],
                    5 => $result['profile'],
                    6 => $result['token_user']
                );
                fputcsv( $worker, $row );
            }

            fclose( $worker );

        } elseif ($table == 'visitors') {

            $filename = $table . $datanow . '.csv';

            header( 'Content-type: application/csv' );   
            header( 'Content-Disposition: attachment; filename=' . $filename);   
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Pragma: no-cache');

            $headerCsv = array(
                0 => "Nome",
                1 => "E-mail",
                2 => "Telefone",
                3 => "Data de Nascimento",
                4 => "Data de Registro",
                5 => "Perfil",
                6 => "Token"
            );
            $data = $this->userModelUtility->listItemVisitorsAll();

            $out = fopen( 'php://output', 'w' );
            fputcsv( $out, $headerCsv );

            foreach ( $data['data'] as $result ) 
            {

                $row = array(
                    0 => $result['name'],
                    1 => $result['email'],
                    2 => $result['phone'],
                    3 => $result['birthday'],
                    4 => date('d/m/Y H:i:s', strtotime($result['visit_date'])),
                    5 => $result['profile'],
                    6 => $result['token_user']
                );
                fputcsv( $out, $row );
            }

            fclose( $out );

        } elseif ($table == 'EE') {

            $filename = $table . $datanow . '.csv';

            header( 'Content-type: application/csv' );   
            header( 'Content-Disposition: attachment; filename=' . $filename);   
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Pragma: no-cache');

            $headerCsv = array(
                0 => "Quem Entrou?",
                1 => "Funcionário que registrou",
                2 => "Entrada",
                3  => "Saída"
            );
            $data = $this->userModelUtility->listItemEEAll();

            $out = fopen( 'php://output', 'w' );
            fputcsv( $out, $headerCsv );

            foreach ( $data['data'] as $results ) 
            {
                $nameWork = $this->userModelUtility->findWorkerForEE($results['id_user_work']);

                $row = array(
                    0 => $nameWork['name'],
                    1 => $results['name'],
                    2 => date('d/m/Y H:i:s', strtotime($results['date_entrance'])),
                    3 => date('d/m/Y H:i:s', strtotime($results['data_exit']))
                );
                fputcsv( $out, $row );
            }

            fclose( $out );

        } else {
            
            $filename = $table . $datanow . '.csv';

            header( 'Content-type: application/csv' );   
            header( 'Content-Disposition: attachment; filename=' . $filename);   
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Pragma: no-cache');

            $headerCsv = array(
                0 => "Nome",
                1 => "E-mail",
                2 => "Telefone",
                3 => "Data de Nascimento",
                4 => "Média Salarial",
                5 => "Perfil",
                6 => "Token"
            );
            $data = $this->userModelUtility->listItemResidentsAll();

            $out = fopen( 'php://output', 'w' );
            fputcsv( $out, $headerCsv );

            foreach ( $data['data'] as $result ) 
            {

                $row = array(
                    0 => $result['name'],
                    1 => $result['email'],
                    2 => $result['phone'],
                    3 => $result['birthday'],
                    4 => number_format($result['avg_income'], 2, ',', '.'),
                    5 => $result['profile'],
                    6 => $result['token_user']
                );
                fputcsv( $out, $row );
            }

            fclose( $out );

        }
        
    }

    #PDF: Exporta relatórios em PDF
    public function exportedInPDF ()
    {

        $this->userModelUtility = new UserModel();

        $this->visits = $this->userModelUtility->listItemVisitorsAll();
        $contVisit = $this->userModelUtility->countVisitsCurrent();

        $this->residents = $this->userModelUtility->listItemResidentsAll();
        $contResidents = $this->userModelUtility->countResidentsCurrent();

        $this->visitCurrent = $this->userModelUtility->listItemEEAll();
        $contVisitCurrent = $this->userModelUtility->countEECurrent();

        $this->workers = $this->userModelUtility->listItemWorkerAll();
        $contWorkers = $this->userModelUtility->countWorkersNotAdmin();

        $this->countEEExit = 0;
        $this->avgIncome = 0;

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $this->configInstance = new Config();
        $pathDef = $this->configInstance->getDirAbsolute();


        $fileName = 'ReportDatasCondominium' . str_replace(':','',date('H:i:s'));

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                $pathDef . '/public/fonts',
            ]),
            'fontdata' => $fontData + [
                'roboto' => [
                    'R' => 'Roboto-Regular.ttf',
                ]
            ],
            'default_font' => 'roboto'
        ]);

        
        $mpdf->imageVars['imageCondominium'] = file_get_contents($pathDef . '/Storage/site/banner/img01.jpg');

        
        $html = '        
            <div id="content">
            
                <div id="mainInfo">
                    
                    <div id="topHome">
                        
                        <div id="topHomeRight">
                            <img src="var:imageCondominium" style="width: 100%;
                            height: 250px;object-fit: cover;" alt="Banner do condominium">
                            <div>
                                 <h1 style="
                                 top: 145px;
                                 right: 70%;
                                 font-size: 1.7em;">Relatório do Condomínio</h1>
                            </div>
                        </div>
        
        
                    </div>
                    <p class="spacingTrue">Este relatório possui informações concentradas e condensados sobre os assuntos pertinentes ao condomínio. Através dele poderá ser visto informações que podem auxiliar na trajetória de desenvolvimento do condomínio.</p>
                    <br>
                    <h1 class="hrColor">Informações Consolidadas</h1>
                    <br>
                    <h3>Total de Funcionários: <span>' . $contWorkers . '</span></h3>
                    <h3>Total de Visitantes: <span>' . $contVisit . '</span></h3>
                    <h3>Total de Residentes: <span>' . $contResidents . '</span></h3>
                    <br>
                    <p>O condomínio tem <span> ' . $contVisitCurrent . '</span> de Visitas.</p>
                    <p>Tem <span>' . $contVisitCurrent . '</span> de Entradas.</p>
                    <p>Tem <span>';

        foreach ($this->visitCurrent['data'] as $field) {
        
            if ($field['data_exit'] != null || $field['data_exit'] != '') {

                $this->countEEExit += 1;

            }

        }
                    
        $html .= $this->countEEExit;
        $html .= '</span> de Saídas.</p>
                    <p>A renda média do condomínio é <span>R$ ';


        foreach ($this->residents['data'] as $field) {
    
            $this->avgIncome += $field['avg_income'];

        }

        $html .= number_format((doubleval($this->avgIncome) / $contResidents), 2, ',', '.');
                    
        $html .= '</span></p>
                </div>
                <div id="contentMain">
                    
                    <br>
                    <h1 class="hrColor">Informações Consolidadas & Detalhadas</h1>
                    <br>
                    <h4>Total de Visitas por Mês</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Mês/Ano</td>
                            <td class="headerTbl">Quantidade de Visitas</td>
                        </tr>';

        $consolidateReportEE = $this->userModelUtility->reportEEConsolidated();

        foreach ($consolidateReportEE as $key) {

            $html .= '
            <tr>
                <td>' . $key['Mes'] . '/' .$key['Ano'] . '</td>
                <td>' . $key['qtd'] . '</td>
            </tr>';

        }
                        
        $html .= '</table>
                    <br>
                    <h4>Total de Visitas por Ano</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Ano</td>
                            <td class="headerTbl">Quantidade de Visitas</td>
                        </tr>';
                        
        
        $consolidateReportEEForYear = $this->userModelUtility->reportEEConsolidatedForYear();

        foreach ($consolidateReportEEForYear as $key) {

            $html .= '
            <tr>
                <td>' . $key['Ano'] . '</td>
                <td>' . $key['qtd'] . '</td>
            </tr>';

        }      
        $html .= '
                    </table>
                    <br>
                    <h4>Faixa de Horários</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Faixa</td>
                            <td class="headerTbl">Quantidade de Entradas</td>
                        </tr>';
                       
        $consolidateReportRanking = $this->userModelUtility->topRankingTimeForQtd();

        foreach ($consolidateReportRanking as $key) {

            $html .= '
            <tr>
                <td>' . $key['faixa'] . '</td>
                <td>' . $key['qtd'] . '</td>
            </tr>';


        }      
        $html .= '
                    </table>
                    <br>
                    <h4>Funcionários X Entradas</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Funcionário</td>
                            <td class="headerTbl">Quantidade de Entradas Registradas</td>
                        </tr>';
                 
        $consolidateReportWorkEE = $this->userModelUtility->workerForEE();

        foreach ($consolidateReportWorkEE as $key) {

            $html .= '
            <tr>
                <td>' . $key['name'] . '</td>
                <td>' . $key['qtd'] . '</td>
            </tr>';


        }   

        $html .= '
                    </table>
                    <br>
                    <br>
                    <h1 class="hrColor">Informações Detalhadas</h1>
                    <br>
                    <h4>Lista de Funcionários</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Nome</td>
                            <td class="headerTbl">E-mail</td>
                            <td class="headerTbl">Telefone</td>
                            <td class="headerTbl">Data de Nascimento</td>
                            <td class="headerTbl">Login</td>
                            <td class="headerTbl">Perfil</td>
                            <td class="headerTbl">Token</td>
                        </tr>';

        foreach ($this->workers['data'] as $key) {

            $html .= '
            <tr>
                <td>' . $key['name'] . '</td>
                <td>' . $key['email'] . '</td>
                <td>' . $key['phone'] . '</td>
                <td>' . date("d/m/Y", strtotime($key['birthday'])) . '</td>
                <td>' . $key['login'] . '</td>
                <td>' . $key['profile'] . '</td>
                <td>' . $key['token_user'] . '</td>
            </tr>';

        }  

        $html .= '
                    </table>
                    <br>
                    <h4>Lista de Visitantes</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Nome</td>
                            <td class="headerTbl">E-mail</td>
                            <td class="headerTbl">Telefone</td>
                            <td class="headerTbl">Data de Nascimento</td>
                            <td class="headerTbl">Data de Registro</td>
                            <td class="headerTbl">Perfil</td>
                            <td class="headerTbl">Token</td>
                        </tr>';
        
        foreach ($this->visits['data'] as $key) {

            $html .= '
            <tr>
                <td>' . $key['name'] . '</td>
                <td>' . $key['email'] . '</td>
                <td>' . $key['phone'] . '</td>
                <td>' . date("d/m/Y", strtotime($key['birthday'])) . '</td>
                <td>' . date("d/m/Y", strtotime($key['visit_date'])) . '</td>
                <td>' . $key['profile'] . '</td>
                <td>' . $key['token_user'] . '</td>
            </tr>';

        }  

        $html .= '
                    </table>
                    <br>
                    <h4>Lista de Residents</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Nome</td>
                            <td class="headerTbl">E-mail</td>
                            <td class="headerTbl">Telefone</td>
                            <td class="headerTbl">Data de Nascimento</td>
                            <td class="headerTbl">Média Salarial</td>
                            <td class="headerTbl">Perfil</td>
                            <td class="headerTbl">Token</td>
                        </tr>';

        foreach ($this->residents['data'] as $key) {

            $html .= '
            <tr>
                <td>' . $key['name'] . '</td>
                <td>' . $key['email'] . '</td>
                <td>' . $key['phone'] . '</td>
                <td>' . date("d/m/Y", strtotime($key['birthday'])) . '</td>
                <td>' . number_format($key['avg_income'], 2, ',', '.') . '</td>
                <td>' . $key['profile'] . '</td>
                <td>' . $key['token_user'] . '</td>
            </tr>';

        }                         

        $html .= '
                    </table>
                    <br>
                    <h4>Lista de Entradas e Saídas</h4>
                    <br>
                    <table class="tblInfo">
                        <tr>
                            <td class="headerTbl">Quem Entrou?</td>
                            <td class="headerTbl">Entrada</td>
                            <td class="headerTbl">Saída</td>
                        </tr>';
                 
        foreach ($this->visitCurrent['data'] as $key) {

            $html .= '
            <tr>
                <td>' . $key['name'] . '</td>
                <td>' . $key['date_entrance'] . '</td>
                <td>' . (($key['data_exit'] == null) ? 'Está no Condomínio' :  $key['data_exit']) . '</td>
            </tr>';

        }


        $html .= '
                    </table>
                    <br>
                    <br>
                    <h5>Resultados atualizadas até ' . date("d/m/Y H:i:s") . '</h5>
                </div>
            
            
            </div>';
            
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetHeader('{DATE j/m/Y}|{PAGENO}/{nbpg}|Condominium'); //Cabeçalho
        $css = file_get_contents($pathDef . '/public/css/styleForPDF.css');

        $mpdf->WriteHTML($css,1); //O css sempre é escrito com o ,1 para o mPDF entender que deve tratar como arquivo csss, o Bootstrap não é aceito, pois ele faz a alteração de todas as tags, impactando nas tags utilizadas pelo padrão do MPDF
        $mpdf->WriteHTML($html); //Gera o corpo
        $mpdf->Output($fileName .'.pdf','D');

    }

    #SEND: Realiza validação e envia email de Forgot
    public function sendEmailForgot ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        try {
            
            Validate::fieldRequired($this->getemail(), "Precisa ser preenchido o campo e-mail");

            Validate::fieldMaximumRequired(
                $this->getemail(), 
                255, 
                "Campo E-mail deve ter no máximo "
            );

            #Validar o formato de email
            Validate::emailFormatValidate(
                $this->getemail(), 
                "Campo E-mail inválido."
            );

            $count = $this->userModelUtility->findEmailWorker($this->getemail());
            $requestForgot = $this->userModelUtility->findEmailForgot($this->getemail());

            if ($count['count'] <= 0 ) {

                $this->response->setStatus(Response::STATUS_500, "Email não encontrado", "","");

            } elseif ($requestForgot >= 1 ) {

                $this->response->setStatus(Response::STATUS_500, "Solicitação em Andamento.", "","");

            } else {

                $email = new SendEmailController();
                $token = md5(date("d/m/Y H:i:s"));

                $this->userModelUtility->registerForgoutPass($count['data']['0']['id_user'], $token);

                $email->send($count['data'], $token);

                $this->response->setStatus(Response::STATUS_200, "Enviado com sucesso", "",
                Config::getHost()
                    );

            }

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }

        return $this->response->resolve();
        exit;

    }

    #UPDATE_FORGOT: Valida e salva a nova senha
    public function sendEmailForgotUpdate ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->userModelUtility = new UserModel();

        try {
            
            Validate::fieldRequired($this->gethash(), "Token Inválido.");

            $count = $this->userModelUtility->validateRequestToken($this->gethash());

            if ($count['count'] <= 0 ) {

                $this->response->setStatus(Response::STATUS_500, "Token Inválido", "","");

            } else {

                Validate::fieldRequired($this->getpassword(), "Campo Senha é requerido.");

                Validate::notContentWhiteSpaceInField($this->getpassword(), "A senha está em branco.");

                Validate::fieldMaximumRequired($this->getpassword(), 70, "A senha pode ter no máximo");

                $this->userModelUtility->updateForgotRequest($this->gethash());

                $password = password_hash($this->getpassword(), PASSWORD_DEFAULT);
                $this->userModelUtility->updatePasswordRequest($count['data']['0']['id_user'], $password);

                $this->response->setStatus(Response::STATUS_200, "Alterado com sucesso", "",
                Config::getHost()
                    );

            }

        } catch (\Exception $e) {
          
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }

        return $this->response->resolve();
        exit;

    }


    
    #MÉTODOS DE FORMATAÇÃO / CALCULO / FUNÇÃO

    #CALCULO: Diferencia da data passada da final
    public function difTimeForgot ($dateStart)
    {

        $datatimeStart = new \DateTime($dateStart);
        $datatimeNow = new \DateTime(date('Y-m-d H:i:s'));
        
        $data1  = $datatimeStart->format('Y-m-d H:i:s');
        $data2  = $datatimeNow->format('Y-m-d H:i:s');
        
        $diff = $datatimeStart->diff($datatimeNow);
        $timeDiff = $diff->h + ($diff->days * 24);

        return $timeDiff;

    }
       
    #FORMATAÇÃO: Define o K, M e B
    /* 
        K >= 1.000 E K <= 999.999,
        M >= 1.000.000 E M <= 999.999.999
        B >= 1.000.000.000
    */
    public function formatNumberInShort ($number)
    {

        $this->countNumberHouse = $this->returnCountHouseNumber(
            $number
        );

        if (strlen($number) <= 3) {

            $this->numberFormated = $number;

        } elseif (strlen($number) > 3 && strlen($number) < 7) {

            $this->numberFormated = substr($number, 0, $this->countNumberHouse) . "K";

        } elseif (strlen($number) >= 7 || strlen($number) < 10) {

            $this->numberFormated = substr($number, 0, $this->countNumberHouse) . "M";

        } elseif (strlen($number) >= 10) {

            $this->numberFormated = substr($number, 0, $this->countNumberHouse) . "B";

        } else {

            $this->numberFormated = "N/A";

        }

        return $this->numberFormated;

    }

    #FORMATAÇÃO: Retorna o numero de casas do valor passado
    public function returnCountHouseNumber ($numberForCount)
    {

        $numberFormated = number_format(boolval($numberForCount), 2, '.', ',');

        $numberFormated = explode('.', $numberFormated);

        return intval(strlen($numberFormated[0]));

    }

    #FUNÇÃO: Faz upload de foto da conta
    public function updateAccountNewPhoto ($request, $response, $args)
    {

        $this->configInstance = new Config();
        $filename =  (isset($args['token']) ? $args['token'] : $args['photoTemp']) . '.jpg';
        $filepath = $this->configInstance->getDirAbsolute() . '/Storage/tmp_photos/';

        $this->fileUploadPhoto = $request->getUploadedFiles();
        $this->fileUploadPhotoObject = $this->fileUploadPhoto['webcam'];

        if (file_exists($filepath . $filename)) {

            unlink($filepath . $filename);

        }

        if (isset($this->fileUploadPhotoObject)) { 

            move_uploaded_file($this->fileUploadPhotoObject->file, $filepath . $filename);

        } 

    }

    #FUNÇÃO: Faz upload para atualização da foto
    public function updateMyPhotoForProfile ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->setData($this->dataForm);

        $this->configInstance = new Config();
        $filepathOrigin = $this->configInstance->getDirAbsolute() . '/Storage/tmp_photos/';
        $filepathDest = $this->configInstance->getDirAbsolute() . '/Storage/';
        $filename =  $this->gettoken_userChangePhoto() . '.jpg';

        try {

            if (file_exists($filepathOrigin . $filename)) {
    
                copy($filepathOrigin . $filename, $filepathDest . $filename);
                
                unlink($filepathOrigin . $filename);
    
            } else {
    
                $this->response->setStatus(Response::STATUS_500, "Usuário e/ou senha inválida.", "","");
                
            }
            
            if ($_SESSION[self::SESSION]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(
                Response::STATUS_200,
                "Alterado com sucesso!",
                "area/" . strval($this->functionAccess) . "/myaccount/" . $_SESSION[self::SESSION]['token_user'],
                Config::getHost()
            );
        
        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

        
    }

    #FUNÇÃO: Faz upload de foto para um novo usuário
    public function updateNewPhotoForUser ($request, $response, $args)
    {

        $this->dataForm = $request->getParsedBody();
        $this->configInstance = new Config();

        $token = $this->dataForm['token'];

        $filepathOrigin = $this->configInstance->getDirAbsolute() . '/Storage/tmp_photos/';
        $filepathOriginForUnlikFiles = $this->configInstance->getDirAbsolute() . '/Storage/tmp_photos';
        
        $filepathDest = $this->configInstance->getDirAbsolute() . '/Storage/';
        $filename =  $token . '.jpg';

        $this->userModelUtility = new UserModel();

        try {

            if (file_exists($filepathOrigin . $filename)) {

                if (file_exists($filepathDest . $filename)) {

                    unlink($filepathDest . $filename);

                }

                copy($filepathOrigin . $filename, $filepathDest . $filename);
                
                $this->configInstance->unlinkRecursive($filepathOriginForUnlikFiles, false);

            } else {

                $this->response->setStatus(Response::STATUS_500, "Tire uma foto primeiro", "",""
                    );
                
            }
        
            $findUser = $this->userModelUtility->findUserWorker(
                $_SESSION[self::SESSION]['login']
            );
            
            if ($findUser[0]['profile'] == '0') {

                $this->functionAccess = 'admin';

            } else {

                $this->functionAccess = 'worker';

            }

            $this->response->setStatus(Response::STATUS_200, "Alterado com sucesso!", "area/" . strval($this->functionAccess) . "/list/" . $args['areaChoice'],
            Config::getHost()
                );

        } catch (\Exception $e) {
        
            $this->response->setStatus(Response::STATUS_500, $e->getMessage());
        
        }
        
        return $this->response->resolve();
        exit;

    }

    #FUNÇÃO: Move a foto da pasta de acordo com o perfil do usuário
    public function movePhotoNewPerfil ($idUser, $photoTemp)
    {

        $this->configInstance = new Config();

        $filepathOrigin = $this->configInstance->getDirAbsolute() . '/Storage/tmp_photos/';
        $filepathOriginForUnlikFiles = $this->configInstance->getDirAbsolute() . '/Storage/tmp_photos';
        
        $filepathDest = $this->configInstance->getDirAbsolute() . '/Storage/';
        $filename =  $photoTemp . '.jpg';

        if (file_exists($filepathOrigin . $filename)) {
    
            rename($filepathOrigin . $filename, $filepathOrigin . $idUser . $filename);

            copy($filepathOrigin . $idUser . $filename, $filepathDest . $idUser . $filename);
            
            $this->configInstance->unlinkRecursive($filepathOriginForUnlikFiles, false);

        } else {

            $this->response->setStatus(Response::STATUS_500, "Tire uma foto primeiro", "","");
            
        }

    }

    #FUNÇÃO: Veirica se tem alguem logado na sessão
    public function verifyUserLogin () {

        if(!isset($_SESSION[UserController::SESSION]) || !$_SESSION[UserController::SESSION] || !(int)$_SESSION[UserController::SESSION]["id_user"] > 0){

            header("Location: " . Config::getHost());
			exit;

        }

    }

}
