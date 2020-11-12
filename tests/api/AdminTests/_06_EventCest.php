<?php


namespace App\Tests\api\AdminTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use function Complex\sec;

class _06_EventCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

    public static function eventName()
    {
        return 'eventTest'.date("Y-m-d");
    }

    public static function getDate()
    {
        return ''.date("d.m.Y");
    }

    public function createEvent(ApiTester $I)
    {
        $eventName = self::eventName();
        $date = self::getDate();
        $id = $I->grabFromDatabase('forms', 'id', ['title' => 'Событие']);
        $region = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);

        $eventData =
            [
            'ke9niadq' =>
                [
                    'Sobytie/Sobytie' =>
                        [[
                            'Data' => ''.$date,
                            'Fail' => [],
                            'Region' => $region,
                            'Prodoljitelnost' => '',
                            'Rezultat' => '',
                            'Tema' => $eventName,
                            'Tip' => null,
                            'Uchastniki' => [],
                            'Vremya' => ''
                        ]]
                ]
        ];

        $json = stripcslashes(json_encode($eventData));

        $I->sendPOST('/api/forms/'.$id.'/send', $json);

        $eventId = $I->grabFromDatabase('pages', 'id', ['title' => $eventName]);

        $eventResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' =>
                    [
                        'redirectUrl' => '\/'.$eventId
                    ]
            ];

        $responseJson = stripcslashes(json_encode($eventResponse));

        $I->seeResponseEquals($responseJson);
    }

    public function getEvent(ApiTester $I)
    {
        $eventName = self::eventName();
        $eventId = $I->grabFromDatabase('pages', 'id', ['title' => $eventName]);

        $I ->sendGET('/api/pages/get/'.$eventId);

        $responseJson = [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' => [
                'id' => $eventId,
                'tags' => ['Событие'],
                'title' => $eventName,
                'blocks' => [],
                'type' => 'page',
                'taskId' => null,
                'projectId' => null
            ]
        ];

        $I->seeResponseContainsJson($responseJson);
    }

    public function getEventTest(ApiTester $I)
    {
        $eventType = $I->grabFromDatabase('object_types', 'id', ['title' => 'Событие']);
        $projectId = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);
        $objectTypeId = $I->grabFromDatabase('forms_objects_fields', 'object_type_field_id', ['title' => 'Проект']);

        $I->sendGET('/api/page/queries?fields['.$objectTypeId.'][]='.$projectId.'&type='.$eventType.'&page=1&limit=25&view_type=table');

        $getEventResponse =
            [
                'objects' => [],
                'pagination' => []
            ];

        $I->seeResponseContainsJson($getEventResponse);
    }

    public function editEvent(ApiTester $I)
    {
        $eventName = self::eventName();
        $date = self::getDate();
        $id = $I->grabFromDatabase('objects', 'id', ['title' => $eventName]);
        $region = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);
        $objectProjId = $I->grabFromDatabase('objects', 'id', ['title' => $eventName]);
        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_ADMIN_USERNAME')]);
        $userName = $I->grabFromDatabase('users', 'username', ['id' => $userId]);

        $eventEditData = [
            'ke9niadq' =>
                [
                    'Sobytie/Sobytie' =>
                        [[
                            'Data' => ''.$date,
                            'Fail' => [],
                            'Region' => $region,
                            'Rezultat' => '',
                            'Tema' => $eventName.'UP',
                            'Tip' => null,
                            'Uchastniki' => [],
                            'Prodoljitelnost' => '',
                            'Vremya' => ''
                        ]]
                ]
        ];

        $json = stripcslashes(json_encode($eventEditData));

        $I->sendPOST('/api/data_objects/'.$id.'/save', $json);

        $typeId = $I->grabFromDatabase('objects', 'type_id', ['title' => $eventName.'UP']);

        $eventEditResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' =>
                    [[
                        'id' => $objectProjId,
                        'title' => $eventName.'UP',
                        'type' => [
                            'id' => $typeId,
                            'title' => 'Событие'
                        ],
                        'creator' => [
                            'id' => $userId,
                            'username' => $userName,
                            'allowPageActions' => true,
                            'fullName' => 'admin test',
                            'homePage' => null
                        ]
                    ]]
            ];

        $I->seeResponseContainsJson($eventEditResponse);
    }

    public function deleteEvent(ApiTester $I)
    {
        $eventName = 'eventTest'.date("Y-m-d");
        $id = $I->grabFromDatabase('pages', 'id', ['title' => $eventName]);

        $I->sendDELETE('/api/pages/'.$id);

        $eventDeleteResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => 'Страница успешно удалена',
                'data' => []
            ];

        $I->seeResponseContainsJson($eventDeleteResponse);
    }
}