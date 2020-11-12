<?php


namespace App\Tests\api\AdminTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use Codeception\Example;

class _08_ObjectCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

    public static function getTaskId(ApiTester $I)
    {
        return $I->grabFromDatabase('object_types', 'id', ['title' => 'Задача']);
    }

    public static function getDataObjectsId(ApiTester $I)
    {
        return $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);
    }

    public function objectsBulkTest(ApiTester $I)
    {
        $projectId = $I->grabFromDatabase('objects', 'id', ['title' => 'test']);

        $objectsBulkData =
            [
                [
                    'id' => $projectId
                ]
        ];

        $json = stripcslashes(json_encode($objectsBulkData));

        $I->sendPOST('/api/data_objects/bulk', $json);

        $objectResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => []
            ];

        $responseJson = stripcslashes(json_encode($objectResponse));

        $I->seeResponseEquals($responseJson);
    }


    public function getTaskObjectId(ApiTester $I)
    {
        $taskId = self::getTaskId($I);

        $I->sendGET('/api/object_types/'.$taskId);

        $taskObjectResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'id' => $taskId,
                    'title' => 'Задача',
                    'name' => null,
                    'titleMask' => null,
                    'sort' => null,
                    'fields' => [],
                    'public' => true,
                    'defaultForm' => []
                ]
            ];

        $I->seeResponseContainsJson($taskObjectResponse);
    }

    public function getTaskObjectIdForm(ApiTester $I)
    {
        $taskId = self::getTaskId($I);
        $defaultForm = $I->grabFromDatabase('object_types', 'default_form', ['title' => 'Задача']);

        $I->sendGET('/api/object_types/'.$taskId.'/form');

        $taskObjectFormResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'data' => [
                        'alias' => 'Zadacha',
                        'public' => true,
                        'title' => 'Задача',
                        'id' => $defaultForm,
                    ],
                    'relations' => [],
                    'values' => [],
                    'formatting' => []
                ]
            ];

        $I->seeResponseContainsJson($taskObjectFormResponse);
    }

    public function getTaskObjectIdObjects(ApiTester $I)
    {
        $taskId = self::getTaskId($I);

        $I->sendGET('/api/object_types/'.$taskId.'/objects');

        $getTaskObjectResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => []
            ];

        $I->seeResponseContainsJson($getTaskObjectResponse);
    }

    public function getDataObjectId(ApiTester $I)
    {
        $dataObjectId = self::getDataObjectsId($I);
        $projectId = $I->grabFromDatabase('object_types', 'id', ['title' => 'Проект']);

        $I->sendGET('/api/data_objects/'.$dataObjectId);

        $getDataObjectResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'id' => $dataObjectId,
                    'values' => [],
                    'type' => [
                        'id' => $projectId
                    ]
                ]
            ];

        $I->seeResponseContainsJson($getDataObjectResponse);
    }

    public function editDataObjectId(ApiTester $I)
    {
        $dataObjectId = self::getDataObjectsId($I);
        $projectId = $I->grabFromDatabase('object_types', 'default_form', ['title' => 'Проект']);

        $I->sendGET('/api/data_objects/'.$dataObjectId.'/edit');

        $editDataObjectResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'data' => [
                        'alias' => 'Proekt',
                        'id' => $projectId
                    ],
                ]
            ];

        $I->seeResponseContainsJson($editDataObjectResponse);
    }
}
