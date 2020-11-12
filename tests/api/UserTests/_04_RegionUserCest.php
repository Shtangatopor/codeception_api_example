<?php


namespace App\Tests\api\UserTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use App\Tests\Helper\DbHelper;

class RegionUserCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_USER_USERNAME'), getenv('TEST_USER_PASSWORD'));
    }

    public static function regionName()
    {
        return 'regionUserTest'.date("Y-m-d");
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

//    public function getRegion(ApiTester $I)
//    {
//        $I->sendGET('/api/page/queries?type=401b107b-4667-4abd-ab81-de140d5b2ddb&view_type=table');
//
//        $regionResponse = [
//            'objects' => [],
//            'pagination' => [
//                'limit' => 25,
//                'currentPage' => 1,
//                'count' => 0,
//                'html' => ''
//            ],
//            'count' => 25,
//            'totalCount' => 0
//        ];
//
//        $responseJson = stripcslashes(json_encode($regionResponse));
//
//        $I->seeResponseEquals($responseJson);
//    }

    public function editRegion(ApiTester $I)
    {
        $regionName = self::regionName();
        $objectRegionId = $I->grabFromDatabase('objects', 'id', ['title' => $regionName]);

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

        $regionEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для редактирования данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($regionEditResponse);
    }

    public function deleteRegion(ApiTester $I)
    {
        $regionName = 'regionUserTest'.date("Y-m-d");
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