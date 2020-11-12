<?php


namespace App\Tests\api;

use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use App\Tests\Helper\DbHelper;

class getCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_CUR_USERNAME'), getenv('TEST_CUR_PASSWORD'));
    }

    public function routeNotFound(ApiTester $I)
    {
        $I->sendGET('/api/not-found');
        $I->seeResponseCodeIs(404);
    }

    public function getTags(ApiTester $I)
    {
        $I->sendGET('/api/tags');

        $data = $I->grabColumnFromDatabase('forms', 'title');

        foreach ($data as $value)
        {
            $I->seeResponseContainsJson(array('data' => array('title' => $value)));
        }
    }

    public function getAccessLevels(ApiTester $I)
    {
        $I->sendGET('/api/wiki_page_block_access_levels');

        $response = [
            "success" => true,
            "code" => 200,
            "message" => "",
            "data" => [
                [
                    "id" => "1",
                    'level' => '1',
                    "description" => "Публичный"
                ],
                [
                    "id" => "2",
                    'level' => '2',
                    "description" => "Ограниченный"
                ],
                [
                    "id" => "3",
                    'level' => '3',
                    "description" => "Служебные поля"
                ]
            ]
        ];

        $I->seeResponseContainsJson($response);
    }

    public function getSearch(ApiTester $I)
    {
        $I->sendGET('/api/search');

        $response = [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' => []
        ];

        $responseJson = stripcslashes(json_encode($response));

        $I->seeResponseEquals($responseJson);
    }

    public function getLabels(ApiTester $I)
    {
        $I->sendGET('/api/labels');

        $response = [
            "success" => true,
            "code" => 200,
            "message" => "",
            "data" => [
                "title" => "Frontend",
                "description" => "Задача frontend разработчика"
            ]
        ];

        $I->seeResponseContainsJson($response);
    }

    public function getUser(ApiTester $I)
    {
        $I->sendGET('/api/user');

        $id = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_CUR_USERNAME')]);

        $response = [
            "success" => true,
            "code" => 200,
            "message" => "",
            "data" => [
                'id' => $id,
                "worktime_start" => null,
                "worktime_end" => null,
                "groups" => [],
                "allowPageActions" => true,
            ]
        ];

        $I->seeResponseContainsJson($response);
    }

    public function getValidAlias(ApiTester $I)
    {
        $I->sendGET('/api/pages/validate_alias?alias=nonono');

        $response = [
            "success" => true,
            "code" => 200,
            "message" => "Алиас прошел валидацию",
            "data" => []
        ];

        $I->seeResponseContainsJson($response);
    }

    public function getForms(ApiTester $I)
    {
        $I->sendGET('/api/forms');

        $data = $I->grabColumnFromDatabase('forms', 'id');

        foreach ($data as $value)
        {
            $I->seeResponseContainsJson(array('data' => array('id' => $value)));
        }
    }

    public function getObjectTypes(ApiTester $I, DbHelper $dbHelper)
    {
        $I->sendGET('/api/object_types');
        $response = $dbHelper->runSqlQuery('select id from object_types order by title ASC limit 10');

        foreach ($response as $value)
        {
            $I->seeResponseContains($value[0]);
        }
    }
}