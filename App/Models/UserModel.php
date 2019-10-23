<?php

namespace App\Models;

use App\Config\Connection;
use PDO;

class UserModel extends Connection
{

    private $connection;
    private $profileNotAdmin = 1;
    private $idUser;
    private $userAccount;

    public function __construct ()
    {

        $this->connection = $this->getConnection();

    }

    public function countUserEmail ($email)
    {

        $consult = $this->connection->prepare("SELECT * FROM users WHERE email = :email");

        $consult->bindParam(':email', $email, PDO::PARAM_STR); 

        $consult->execute();

        return $consult->rowCount();

    }

    public function findUserForAccount ($token)
    {

        $consult = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE u.token_user = :token");

        $consult->bindParam(':token', $token, PDO::PARAM_STR); 

        $consult->execute();

        $result = $consult->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    public function updateMyAccountDB ($data, $options = "Yes") 
    {

        $this->idUser = $this->findUserForAccount($data['token_user']);

        $updateUser = $this->connection->prepare("UPDATE users SET 
        name = :name,
        email = :email,
        rg = :rg,
        cpf = :cpf,
        phone = :phone,
        birthday = :birthday
        WHERE id_user = :id_user
        ");

        $dataFormated = date("Y-m-d", strtotime($data['birthday']));
        $phone = str_replace('(', '', $data['phone']);
        $phone = str_replace(')', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('-', '', $phone);

        $id = intval($this->idUser['0']['id_user']);

        $updateUser->bindParam(':id_user', $id, PDO::PARAM_INT); 
        $updateUser->bindParam(':name', $data['name'], PDO::PARAM_STR); 
        $updateUser->bindParam(':email', $data['fldEmail'], PDO::PARAM_STR); 
        $updateUser->bindParam(':rg', $data['rg'], PDO::PARAM_STR);
        $updateUser->bindParam(':cpf', $data['cpf'], PDO::PARAM_STR);
        $updateUser->bindParam(':phone', $phone, PDO::PARAM_STR);
        $updateUser->bindParam(':birthday', $dataFormated, PDO::PARAM_STR);

        $updateUser->execute();

        $id = intval($this->idUser['0']['id_user']);

        if ($options == "Yes") {

            $updatePass = $this->connection->prepare("UPDATE workers SET 
            login = :login,
            password = :password
            WHERE id_user = :id_user");

            $pass = password_hash($data['fldPassword'], PASSWORD_DEFAULT);
            
            $updatePass->bindParam(':id_user', $id, PDO::PARAM_INT); 
            $updatePass->bindParam(':login', $data['fldLogin'], PDO::PARAM_STR); 
            $updatePass->bindParam(':password', $pass, PDO::PARAM_STR); 

            $updatePass->execute();

        } else {

            $updatePass = $this->connection->prepare("UPDATE workers SET 
            login = :login
            WHERE id_user = :id_user");
            
            $updatePass->bindParam(':id_user', $id, PDO::PARAM_INT); 
            $updatePass->bindParam(':login', $data['fldLogin'], PDO::PARAM_STR); 

            $updatePass->execute();

        }


    }

    public function insertUserInDB ($data = array(), $profile)
    {
        $insertUser = $this->connection->prepare("INSERT INTO users (
        name,
        email,
        rg,
        cpf,
        phone,
        birthday,
        record_date,
        profile
        ) VALUES (
        :name,
        :email,
        :rg,
        :cpf,
        :phone,
        :birthday,
        :record_date,
        :profile
        )
        ");

        $dataFormated = date("Y-m-d", strtotime($data['birthday']));
        $phone = str_replace('(', '', $data['phone']);
        $phone = str_replace(')', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('-', '', $phone);
        $dataRecord = date('Y-m-d H:i:s');
 
        $insertUser->bindParam(':name', $data['name'], PDO::PARAM_STR); 
        $insertUser->bindParam(':email', $data['fldEmail'], PDO::PARAM_STR); 
        $insertUser->bindParam(':rg', $data['rg'], PDO::PARAM_STR);
        $insertUser->bindParam(':cpf', $data['cpf'], PDO::PARAM_STR);
        $insertUser->bindParam(':phone', $phone, PDO::PARAM_STR);
        $insertUser->bindParam(':birthday', $dataFormated, PDO::PARAM_STR);
        $insertUser->bindParam(':record_date', $dataRecord, PDO::PARAM_STR);
        $insertUser->bindParam(':profile', $profile, PDO::PARAM_STR);

        $insertUser->execute();

        return $this->connection->lastInsertId();

    }

    
    public function updateUserForResidentsDB ($data)
    {

        $this->userAccount = $this->findUserForAccount($data['token_userForResidents']);

        
        $insertResident = $this->connection->prepare("INSERT INTO residents (
            id_user,
            avg_income
        ) VALUES (
            :id_user,
            :avg_income
        )");

        
        $avgIncome = str_replace('.', '', $data['avgIncome']);
        $avgIncome = str_replace(',', '.', $avgIncome);
        $avgIncome = doubleval($avgIncome);
        $id = intval($this->userAccount['0']['id_user']);
        
        $insertResident->bindParam(':id_user', $id, PDO::PARAM_INT); 
        $insertResident->bindParam(':avg_income', $avgIncome, PDO::PARAM_STR);

        $insertResident->execute();

    }


    public function updateTokenUser ($lastIdInsert, $token)
    {

        $updateUserToken = $this->connection->prepare("UPDATE users SET 
            token_user = :token_user
            WHERE id_user = :id_user");

        $updateUserToken->bindParam(':token_user', $token, PDO::PARAM_STR); 
        $updateUserToken->bindParam(':id_user', $lastIdInsert, PDO::PARAM_STR); 

        $updateUserToken->execute();

    }


    public function searchDocument ($document)
    {

        $results = $this->connection->prepare("SELECT * FROM users 
        WHERE rg = :document OR cpf = :document LIMIT 1");

        $results->bindParam(':document', $document, PDO::PARAM_STR);
        
        $results->execute();

        $resultsEntrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);
        $resultsCount = $results->rowCount();

        return array(
            'countUser' => intval($resultsCount),
            'data' => ($resultsEntrancesAndExits != null) ? $resultsEntrancesAndExits : ''
        );

    }

    public function searchDocumentExit ($document)
    {

        $results = $this->connection->prepare("SELECT ee.*, u.* FROM entrances_and_exits as ee
        INNER JOIN users as u ON ee.id_vr = u.id_user
        WHERE ee.data_exit is NULL AND (u.rg = :document OR u.cpf = :document) ORDER BY id_EE ASC LIMIT 1");

        $results->bindParam(':document', $document, PDO::PARAM_STR);
        
        $results->execute();

        $resultsEntrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);
        $resultsCount = $results->rowCount();

        return array(
            'countUser' => intval($resultsCount),
            'data' => ($resultsEntrancesAndExits != null) ? $resultsEntrancesAndExits : ''
        );

    }

    public function deletedItem ($id, $area)
    {

        if ($area == 'visit') {

            $results = $this->connection->prepare("DELETE FROM visitors WHERE id_visitors = :id_visitors");

            $results->bindParam(':id_visitors', $id, PDO::PARAM_INT); 

            $results->execute();

        } elseif ($area == 'resident') {

            $results = $this->connection->prepare("DELETE FROM residents WHERE id_residents = :id_residents");

            $results->bindParam(':id_residents', $id, PDO::PARAM_INT); 

            $results->execute();

        } elseif ($area == 'EE') {

            $results = $this->connection->prepare("DELETE FROM entrances_and_exits WHERE id_EE = :id_EE");

            $results->bindParam(':id_EE', $id, PDO::PARAM_INT); 

            $results->execute();

        } else {

            $results = $this->connection->prepare("DELETE FROM workers WHERE id_worker = :id_worker");

            $results->bindParam(':id_worker', $id, PDO::PARAM_INT); 

            $results->execute();

        }

    }

    public function reportEEConsolidated ()
    {

        $results = $this->connection->prepare("SELECT YEAR(date_entrance) as Ano, MONTH(date_entrance) as Mes, count(*) as qtd FROM entrances_and_exits GROUP BY YEAR(date_entrance), MONTH(date_entrance)");
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);

        return $entrancesAndExits;

    }

    public function reportEEConsolidatedForYear ()
    {

        $results = $this->connection->prepare("SELECT YEAR(date_entrance) as Ano, count(*) as qtd FROM entrances_and_exits GROUP BY YEAR(date_entrance)");
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);

        return $entrancesAndExits;

    }

    public function topRankingTimeForQtd ()
    {

        $results = $this->connection->prepare("SELECT faixa,count(*) as qtd
        FROM (
          SELECT 
          (CASE
          WHEN DATE_FORMAT(date_entrance, '%H:%i:%s') > '07:00' 
          && DATE_FORMAT(date_entrance, '%H:%i:%s') < '09:59:59' THEN '07h às 09h'
          WHEN DATE_FORMAT(date_entrance, '%H:%i:%s') > '12:00' 
          && DATE_FORMAT(date_entrance, '%H:%i:%s') < '14:59:59' THEN '12h às 14h'
          WHEN DATE_FORMAT(date_entrance, '%H:%i:%s') > '19:00' 
          && DATE_FORMAT(date_entrance, '%H:%i:%s') < '21:59:59' THEN '19h às 21h'
          WHEN DATE_FORMAT(date_entrance, '%H:%i:%s') > '21:00' 
          && DATE_FORMAT(date_entrance, '%H:%i:%s') < '23:59:59' THEN '21h às 23h'
           ELSE 0 END)
          AS faixa
          FROM entrances_and_exits
        ) as acessos    
        GROUP BY faixa");
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);

        return $entrancesAndExits;

    }

    public function workerForEE ()
    {

        $results = $this->connection->prepare("SELECT u.name, COUNT(ee.id_user_work) as qtd FROM entrances_and_exits as ee INNER JOIN users as u ON (ee.id_user_work = u.id_user) GROUP BY ee.id_user_work;"); 
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);

        return $entrancesAndExits;

    }

    public function findEmailWorker ($email)
    {
        
        $consult = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE u.email = :email");

        $consult->bindParam(':email', $email, PDO::PARAM_STR); 

        $consult->execute();

        $dataUser = $consult->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'count' => $consult->rowCount(),
            'data' => $dataUser
        );

    }

    public function findEmailForgot ($email)
    {
        
        $consult = $this->connection->prepare("SELECT f.*, u.* FROM forgots_passwords as f
        INNER JOIN users as u ON f.id_user = u.id_user
        WHERE u.email = :email AND f.status_forgot_pass = :status_forgot_pass");

        $statusConsult = 1;

        $consult->bindParam(':email', $email, PDO::PARAM_STR); 
        $consult->bindParam(':status_forgot_pass', $statusConsult, PDO::PARAM_INT); 

        $consult->execute();

        return $consult->rowCount();

    }

    public function registerForgoutPass ($id, $token)
    {

        $data = date("Y-m-d H:i:s");
        $status = 1;

        $consult = $this->connection->prepare("INSERT INTO forgots_passwords (id_user, token_forgot_pass, request_forgot_date, status_forgot_pass) VALUES (:id_user, :token_forgot_pass, :request_forgot_date, :status_forgot_pass)");

        $consult->bindParam(':id_user', $id, PDO::PARAM_INT); 
        $consult->bindParam(':token_forgot_pass', $token, PDO::PARAM_STR); 
        $consult->bindParam(':request_forgot_date', $data, PDO::PARAM_STR); 
        $consult->bindParam(':status_forgot_pass', $status, PDO::PARAM_STR); 

        $consult->execute();

    }

    public function validateRequestToken ($token)
    {

        $consult = $this->connection->prepare("SELECT * FROM forgots_passwords WHERE token_forgot_pass = :token_forgot_pass");

        $consult->bindParam(':token_forgot_pass', $token, PDO::PARAM_STR); 

        $consult->execute();

        $dataRequest = $consult->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'count' => $consult->rowCount(),
            'data' => $dataRequest
        );

    }

    public function updateForgotRequest ($token)
    {

        $consult = $this->connection->prepare("UPDATE forgots_passwords SET status_forgot_pass = :status_forgot_pass WHERE token_forgot_pass = :token_forgot_pass");

        $status = 0;

        $consult->bindParam(':status_forgot_pass', $status, PDO::PARAM_STR); 
        $consult->bindParam(':token_forgot_pass', $token, PDO::PARAM_STR); 

        $consult->execute();

    }

    public function updatePasswordRequest ($id, $pass)
    {
        
        $consult = $this->connection->prepare("UPDATE workers SET password = :password WHERE id_user = :id_user");

        $status = 0;

        $consult->bindParam(':id_user', $id, PDO::PARAM_STR); 
        $consult->bindParam(':password', $pass, PDO::PARAM_STR); 

        $consult->execute();

    }

    public function updateItemUser ($data, $idUser)
    {

        $updateUser = $this->connection->prepare("UPDATE users SET 
        name = :name,
        email = :email,
        rg = :rg,
        cpf = :cpf,
        phone = :phone,
        birthday = :birthday
        WHERE id_user = :id_user
        ");

        $dataFormated = date("Y-m-d", strtotime($data['birthday']));
        $phone = str_replace('(', '', $data['phone']);
        $phone = str_replace(')', '', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('-', '', $phone);
        $idUser = intval($idUser);

        $updateUser->bindParam(':id_user', $idUser, PDO::PARAM_INT); 
        $updateUser->bindParam(':name', $data['name'], PDO::PARAM_STR); 
        $updateUser->bindParam(':email', $data['fldEmail'], PDO::PARAM_STR); 
        $updateUser->bindParam(':rg', $data['rg'], PDO::PARAM_STR);
        $updateUser->bindParam(':cpf', $data['cpf'], PDO::PARAM_STR);
        $updateUser->bindParam(':phone', $phone, PDO::PARAM_STR);
        $updateUser->bindParam(':birthday', $dataFormated, PDO::PARAM_STR);

        $updateUser->execute();

    }


    #WORKERS
    public function countUserWorker ($login)
    {

        $consult = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE w.login = :login");

        $consult->bindParam(':login', $login, PDO::PARAM_STR); 

        $consult->execute();

        return $consult->rowCount();

    }

    public function findUserWorker ($login)
    {


        $consult = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE w.login = :login");

        $consult->bindParam(':login', $login, PDO::PARAM_STR); 

        $consult->execute();

        $result = $consult->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    public function findWorkerDB ($id)
    {

        $results = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE w.id_worker = :id_worker");

        $id = intval($id);

        $results->bindParam(':id_worker', $id, PDO::PARAM_INT);
        
        $results->execute();

        $worker = $results->fetchAll(PDO::FETCH_ASSOC);

        return $worker;

    }

    public function countWorkersNotAdmin ()
    {

        $consult = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE u.profile = :profile");

        $consult->bindParam(':profile', $this->profileNotAdmin, PDO::PARAM_INT); 

        $consult->execute();

        return $consult->rowCount();

    }

    public function createWorkerDB ($id, $data)
    {

        $insertWorker = $this->connection->prepare("INSERT INTO workers (
            id_user,
            login,
            password
            ) VALUES (
            :id_user,
            :login,
            :password
            )");

        $idUser = intval($id);
        $passwordHash = password_hash($data['fldPassword'], PASSWORD_DEFAULT);

        $insertWorker->bindParam(':id_user', $idUser, PDO::PARAM_INT); 
        $insertWorker->bindParam(':login', $data['fldLogin'], PDO::PARAM_STR); 
        $insertWorker->bindParam(':password', $passwordHash, PDO::PARAM_STR);

        $insertWorker->execute();

    }

    public function listItemWorker ($start, $totalReg, $search = '')
    {

        $results = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE u.profile = :profile AND (u.name LIKE :search OR (u.rg LIKE :search OR (u.cpf LIKE :search OR (w.login LIKE :search)))) LIMIT :startpage,:totalreg");

        $search = '%' . $search . '%';

        $results->bindParam(':profile', $this->profileNotAdmin, PDO::PARAM_INT); 
        $results->bindParam(':search', $search, PDO::PARAM_STR);
        $results->bindParam(':startpage', $start, PDO::PARAM_INT);
        $results->bindParam(':totalreg', $totalReg, PDO::PARAM_INT);
        
        $results->execute();

        $workers = $results->fetchAll(PDO::FETCH_ASSOC);

        return $workers;

    }

    public function updateItemWorker ($data, $idUser, $opt = 0)
    {

        if ($opt == 0) {

            $updateWorker = $this->connection->prepare("UPDATE workers SET 
                    login = :login
                    WHERE id_worker = :id_worker
                    ");
            

            $updateWorker->bindParam(':id_worker', $idUser, PDO::PARAM_INT); 
            $updateWorker->bindParam(':login', $data['fldLogin'], PDO::PARAM_STR); 

        } else {

            $updateWorker = $this->connection->prepare("UPDATE workers SET 
                    login = :login,
                    password = :password
                    WHERE id_worker = :id_worker
                    ");
            
            $passWorker = password_hash($data['fldPassword'], PASSWORD_DEFAULT);

            $updateWorker->bindParam(':id_worker', $idUser, PDO::PARAM_INT); 
            $updateWorker->bindParam(':login', $data['fldLogin'], PDO::PARAM_STR); 
            $updateWorker->bindParam(':password', $passWorker, PDO::PARAM_STR); 

        }

    }

    public function listItemWorkerAll ()
    {

        $results = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE u.profile = :profile");

        $results->bindParam(':profile', $this->profileNotAdmin, PDO::PARAM_INT); 
        
        $results->execute();

        $workers = $results->fetchAll(PDO::FETCH_ASSOC);
        $workersCount = $results->rowCount();

        return array(
            'data' => $workers,
            'count' => $workersCount
        );

    }

    public function findWorkerForEE ($idWorker)
    {

        $results = $this->connection->prepare("SELECT w.*, u.* FROM workers as w
        INNER JOIN users as u ON w.id_user = u.id_user
        WHERE u.profile = :profile AND w.id_worker = :id_worker");

        $results->bindParam(':profile', $this->profileNotAdmin, PDO::PARAM_INT); 
        $results->bindParam(':id_worker', $idWorker, PDO::PARAM_INT); 
        
        $results->execute();

        $entrancesAndExitsWorker = $results->fetchAll(PDO::FETCH_ASSOC);

        return $entrancesAndExitsWorker;

    }

    
    #VISITORS
    public function countVisitsCurrent ()
    {

        $consult = $this->connection->prepare("SELECT v.*, u.* FROM visitors as v
        INNER JOIN users as u ON v.id_user = u.id_user");

        $consult->execute();

        return $consult->rowCount();

    }

    public function consultIsAVisit ($id)
    {

        $consult = $this->connection->prepare("SELECT r.*, u.* FROM residents as r
        INNER JOIN users as u ON r.id_user = u.id_user
        WHERE r.id_user = :id_user");

        $consult->bindParam(':id_user', $id, PDO::PARAM_INT); 

        $consult->execute();

        return $consult->rowCount();

    }

    public function createVisitantDB ($id, $data)
    {

        $insertVisitant = $this->connection->prepare("INSERT INTO visitors (
            id_user,
            visit_date
            ) VALUES (
            :id_user,
            :visit_date
            )");

        $idUser = intval($id);
        $visitFirst = date('Y-m-d H:i:s');

        $insertVisitant->bindParam(':id_user', $idUser, PDO::PARAM_INT); 
        $insertVisitant->bindParam(':visit_date', $visitFirst, PDO::PARAM_STR);

        $insertVisitant->execute();

    }

    public function listItemVisitors ($start, $totalReg, $search = '')
    {

        $results = $this->connection->prepare("SELECT v.*, u.* FROM visitors as v
        INNER JOIN users as u ON v.id_user = u.id_user
        WHERE u.name LIKE :search OR (u.rg LIKE :search OR (u.cpf LIKE :search)) LIMIT :startpage,:totalreg");

        $search = '%' . $search . '%';

        $results->bindParam(':profile', $this->profileNotAdmin, PDO::PARAM_INT); 
        $results->bindParam(':search', $search, PDO::PARAM_STR);
        $results->bindParam(':startpage', $start, PDO::PARAM_INT);
        $results->bindParam(':totalreg', $totalReg, PDO::PARAM_INT);
        
        $results->execute();

        $visitors = $results->fetchAll(PDO::FETCH_ASSOC);

        return $visitors;

    }

    public function findVisitDB ($id)
    {

        $results = $this->connection->prepare("SELECT v.*, u.* FROM visitors as v
        INNER JOIN users as u ON v.id_user = u.id_user
        WHERE v.id_visitors = :id_visitors");

        $id = intval($id);

        $results->bindParam(':id_visitors', $id, PDO::PARAM_INT);
        
        $results->execute();

        $visit = $results->fetchAll(PDO::FETCH_ASSOC);

        return $visit;

    }

    public function listItemVisitorsAll ()
    {

        $results = $this->connection->prepare("SELECT v.*, u.* FROM visitors as v
        INNER JOIN users as u ON v.id_user = u.id_user
        WHERE u.profile = :profile");

        $results->bindParam(':profile', $this->profileNotAdmin, PDO::PARAM_INT); 
        
        $results->execute();

        $visitors = $results->fetchAll(PDO::FETCH_ASSOC);
        $visitorsCount = $results->rowCount();

        return array(
            'data' => $visitors,
            'count' => $visitorsCount
        );

    }



    #RESIDENTS
    public function countResidentsCurrent ()
    {

        $consult = $this->connection->prepare("SELECT r.*, u.* FROM residents as r
        INNER JOIN users as u ON r.id_user = u.id_user");

        $consult->execute();

        return $consult->rowCount();

    }

    public function listItemResidents ($start, $totalReg, $search = '')
    {

        $results = $this->connection->prepare("SELECT r.*, u.* FROM residents as r
        INNER JOIN users as u ON r.id_user = u.id_user
        WHERE u.name LIKE :search OR (u.rg LIKE :search OR (u.cpf LIKE :search OR (r.avg_income LIKE :search))) LIMIT :startpage,:totalreg");

        $search = '%' . $search . '%';

        $results->bindParam(':search', $search, PDO::PARAM_STR);
        $results->bindParam(':startpage', $start, PDO::PARAM_INT);
        $results->bindParam(':totalreg', $totalReg, PDO::PARAM_INT);
        
        $results->execute();

        $residents = $results->fetchAll(PDO::FETCH_ASSOC);

        return $residents;

    }

    public function createResidentDB ($id, $data)
    {

        $insertResident = $this->connection->prepare("INSERT INTO residents (
            id_user,
            avg_income
            ) VALUES (
            :id_user,
            :avg_income
            )");

        $idUser = intval($id);
        $avgIncome = str_replace('.', '', $data['avgIncome']);
        $avgIncome = str_replace(',', '.', $avgIncome);
        $avgIncome = doubleval($avgIncome);

        $insertResident->bindParam(':id_user', $idUser, PDO::PARAM_INT); 
        $insertResident->bindParam(':avg_income', $avgIncome, PDO::PARAM_STR);

        $insertResident->execute();

    }

    public function updateItemResidents ($data, $idUser)
    {

        $updateResident = $this->connection->prepare("UPDATE residents SET 
                avg_income = :avg_income
                WHERE id_residents = :id_residents
                ");
        
        $priceNew = number_format(boolval($data['avgIncome']), 2, '.', '');

        $updateResident->bindParam(':id_residents', $idUser, PDO::PARAM_INT); 
        $updateResident->bindParam(':avg_income', $priceNew, PDO::PARAM_STR); 

    }

    public function findResidentDB ($id)
    {

        $results = $this->connection->prepare("SELECT r.*, u.* FROM residents as r
        INNER JOIN users as u ON r.id_user = u.id_user
        WHERE r.id_residents = :id_residents");

        $id = intval($id);

        $results->bindParam(':id_residents', $id, PDO::PARAM_INT);
        
        $results->execute();

        $resident = $results->fetchAll(PDO::FETCH_ASSOC);

        return $resident;

    }

    public function listItemResidentsAll ()
    {

        $results = $this->connection->prepare("SELECT r.*, u.* FROM residents as r
        INNER JOIN users as u ON r.id_user = u.id_user");
        
        $results->execute();

        $residents = $results->fetchAll(PDO::FETCH_ASSOC);
        $residentsCount = $results->rowCount();

        return array(
            'data' => $residents,
            'count' => $residentsCount
        );

    }

    
    #EE
    public function countEECurrent ()
    {

        $consult = $this->connection->prepare("SELECT e.*, u.* FROM entrances_and_exits as e
        INNER JOIN users as u ON e.id_vr = u.id_user");

        $consult->execute();

        return $consult->rowCount();

    }
    
    public function listItemEE ($start, $totalReg, $search = '')
    {

        $results = $this->connection->prepare("SELECT ee.*, u.* FROM entrances_and_exits as ee
        INNER JOIN users as u ON ee.id_vr = u.id_user
        WHERE u.name LIKE :search OR (u.rg LIKE :search OR (u.cpf LIKE :search)) LIMIT :startpage,:totalreg");

        $search = '%' . $search . '%';

        $results->bindParam(':search', $search, PDO::PARAM_STR);
        $results->bindParam(':startpage', $start, PDO::PARAM_INT);
        $results->bindParam(':totalreg', $totalReg, PDO::PARAM_INT);
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);

        return $entrancesAndExits;

    }

    public function findEEDB ($id)
    {

        $results = $this->connection->prepare("SELECT ee.*, u.* FROM entrances_and_exits as ee
        INNER JOIN users as u ON ee.id_vr = u.id_user
        WHERE ee.id_EE = :id_EE");

        $id = intval($id);

        $results->bindParam(':id_EE', $id, PDO::PARAM_INT);
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);

        $resultsWorker = $this->connection->prepare("SELECT ee.*, u.* FROM entrances_and_exits as ee
        INNER JOIN users as u ON ee.id_user_work = u.id_user
        WHERE ee.id_EE = :id_EE");

        $resultsWorker->bindParam(':id_EE', $id, PDO::PARAM_INT);
        
        $resultsWorker->execute();

        $entrancesAndExitsWorkers = $resultsWorker->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'user' => $entrancesAndExits,
            'worker' => $entrancesAndExitsWorkers
        );

    }


    public function storageEntranceDB ($idWorker, $data)
    {

        $results = $this->connection->prepare("INSERT INTO entrances_and_exits (
            id_user_work,
            id_vr,
            date_entrance,
            description
        ) VALUES (
            :id_user_work,
            :id_vr,
            :date_entrance,
            :description
        )");

        $dataNow = date("Y-m-d H:i:s");

        $results->bindParam(':id_user_work', $idWorker, PDO::PARAM_INT); 
        $results->bindParam(':id_vr', $data['idUser'], PDO::PARAM_INT); 
        $results->bindParam(':date_entrance', $dataNow, PDO::PARAM_STR); 
        $results->bindParam(':description', $data['descriptionEE'], PDO::PARAM_STR);
        
        $results->execute();

    }


    public function storageExitDB ($idWorker, $data, $opt = 0)
    {

        if ($opt == 0) {

            $results = $this->connection->prepare("UPDATE entrances_and_exits SET description = :description WHERE id_EE = :id_EE");
    
            $results->bindParam(':id_EE', $data['idEEUpdate'], PDO::PARAM_INT); 
            $results->bindParam(':description', $data['descriptionEE'], PDO::PARAM_STR);

            $results->execute();

        } else {

            $results = $this->connection->prepare("UPDATE entrances_and_exits SET description = :description, data_exit = :data_exit WHERE id_EE = :id_EE");
    
            $dataNow = date('Y-m-d H:i:s');

            $results->bindParam(':id_EE', $data['idEEUpdate'], PDO::PARAM_INT); 
            $results->bindParam(':description', $data['descriptionEE'], PDO::PARAM_STR);
            $results->bindParam(':data_exit', $dataNow, PDO::PARAM_STR);

            $results->execute();

        }

    }

    public function listItemEEAll ()
    {

        $results = $this->connection->prepare("SELECT ee.*, u.* FROM entrances_and_exits as ee
        INNER JOIN users as u ON ee.id_vr = u.id_user"); 
        
        $results->execute();

        $entrancesAndExits = $results->fetchAll(PDO::FETCH_ASSOC);
        $entrancesAndExitsCount = $results->rowCount();

        return array(
            'data' => $entrancesAndExits,
            'count' => $entrancesAndExitsCount,
        );

    }


}
