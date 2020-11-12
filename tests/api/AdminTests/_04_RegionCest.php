<?php


namespace App\Tests\api\AdminTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use App\Tests\Helper\DbHelper;

class _04_RegionCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

    public static function regionName()
    {
        return 'regionTest'.date("Y-m-d");
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

        $regionId = $I->grabFromDatabase('pages', 'id', ['title' => $regionName]);

        $regionResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'redirectUrl' => '\/'.$regionId
                ]
            ];

        $responseJson = stripcslashes(json_encode($regionResponse));

        $I->seeResponseEquals($responseJson);
    }

//    public function getRegion(ApiTester $I, DbHelper $dbHelper)
//    {
//        $regionId = $I->grabFromDatabase('object_types', 'id', ['title' => 'Проект']);
//
//        $I->sendGET('/api/page/queries?type='.$regionId);
//
//        $groups = $dbHelper->runSqlQuery("select title from groups where id not in ('27', '29', '121', '125')");
//
//        foreach ($groups as $group)
//        {
//            $I->seeResponseContainsJson(array('objects' => array(array('title' => $group[0]))));
//        }
//    }

    public function editRegion(ApiTester $I)
    {
        $regionName = self::regionName();
        $objectRegionId = $I->grabFromDatabase('objects', 'id', ['title' => $regionName]);
        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_ADMIN_USERNAME')]);
        $userName = $I->grabFromDatabase('users', 'username', ['id' => $userId]);

        $regionEditData =
            [
            'ke9nx9yn' =>
                [
                    'Region/Region' =>
                        [[
                            'Izobrajenie' => '',
                            'Kurator' => '',
                            'Nazvanie_regiona' => $regionName.'UP',
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
        $regionName = 'regionTest'.date("Y-m-d");
        $regionId = $I->grabFromDatabase('pages', 'id', ['title' => $regionName]);

        $I->sendDELETE('/api/pages/'.$regionId);

        $regionDeleteResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => 'Страница успешно удалена',
                'data' => []
            ];

        $I->seeResponseContainsJson($regionDeleteResponse);
    }
}