<?php


namespace App\Tests\api;

use App\Tests\ApiTester;
use App\Tests\Helper\DbHelper;

class dbConnectCest
{
    public function _before()
    {
    }

    public function tryToTest(ApiTester $I)
    {
        $id = $I->grabFromDatabase('forms', 'id', array('title' => 'Задача'));
        var_dump($id);
    }

    public function getPersonListToAdmin(DbHelper $dbHelper)
    {
        // список персон для админа
        // /wiki/page/queries?type=c630742c-0d48-4bb4-b315-3c88127ed090
        // 44 для новой тестовой базы, в списке сотрудники из 3х регионов
        // Поручения генерального директора, Техническая поддержка, Центр управления регионами
        // никаких админов итп.

        // вот так мы можем взять юзеров трех регионов
        $user_list =  $dbHelper->runSqlQuery('
            select  distinct(id), users.object_id, lastname, firstname from users left join users_groups ug on users.id = ug.user_id where group_id in (102, 106, 115)
        ');

        dd($user_list);

        // 44 штуки !
    }
}