<?php


namespace App\Tests\api\UserCurTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use App\Tests\Helper\DbHelper;

class RegionCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_CUR_USERNAME'), getenv('TEST_CUR_PASSWORD'));
    }

    public static function regionName()
    {
        return 'regionCurTest'.date("Y-m-d");
    }

    public function createRegion(ApiTester $I)
    {
        $regionName = self::regionName();
        $id = $I->grabFromDatabase('forms', 'id', ['title' => 'Проект']);

        $regionData =
            [
                'ke9nx9yn' =>
                    [
                        'Region/Region' =>
                            [[
                                'Izobrajenie' => '',
                                'Kurator' => '',
                                'Nazvanie' => $regionName,
                                'Roditelskii_proekt' => ''
                            ]]
                    ]
            ];

        $json = stripcslashes(json_encode($regionData));

        $I->sendPOST('/api/forms/'.$id.'/send', $json);

        $regionResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для просмотра данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($regionResponse);
    }

//    public function getRegion(ApiTester $I, DbHelper $dbHelper)
//    {
//        $regionId = $I->grabFromDatabase('object_types', 'id', ['title' => 'Проект']);
//
//        $I->sendGET('/api/page/queries?type='.$regionId);
//
//        $groups = $dbHelper->runSqlQuery("select title from groups where id = '121'");
//
//        dd($groups);
//
////        foreach ($groups as $group)
////        {
////            $I->seeResponseContainsJson(array('objects' => array(array('title' => $group[0]))));
////        }
//    }

    public function editRegion(ApiTester $I)
    {
        $regionName = self::regionName();
        $objectRegionId = $I->grabFromDatabase('objects', 'id', ['title' => $regionName]);
        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_CUR_USERNAME')]);
        $userName = $I->grabFromDatabase('users', 'username', ['id' => $userId]);

        $regionEditData =
            [
                'ke9nx9yn' =>
                    [
                        'Region/Region' =>
                            [[
                                'Izobrajenie' => '',
                                'Kurator' => '',
                                'Nazvanie' => $regionName.'UP',
                                'Roditelskii_proekt' => ''
                            ]]
                    ]
            ];

        $json = stripcslashes(json_encode($regionEditData));

        $I->sendPOST('/api/data_objects/'.$objectRegionId.'/save', $json);

        $typeId = $I->grabFromDatabase('objects', 'type_id', ['title' => $regionName.'UP']);

        $regionEditResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [[
                    'id' => $objectRegionId,
                    'title' => $regionName.'UP',
                    "type" => [
                        'id' => $typeId,
                        'title' => 'Проект'
                    ],
                    'creator' => [
                        'id' => $userId,
                        'username' => $userName
                    ]
                ]]
            ];

        $I->seeResponseContainsJson($regionEditResponse);
    }

    public function deleteRegion(ApiTester $I)
    {
        $regionName = 'regionCurTest'.date("Y-m-d");
        $regionId = $I->grabFromDatabase('pages', 'id', ['title' => $regionName]);

        $I->sendDELETE('/api/pages/'.$regionId);

        $regionResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для удаления данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($regionResponse);
    }
}