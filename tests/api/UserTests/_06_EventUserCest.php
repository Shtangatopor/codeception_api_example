<?php


namespace App\Tests\api\UserTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class EventUserCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_USER_USERNAME'), getenv('TEST_USER_PASSWORD'));
    }

    public static function eventName()
    {
        return 'eventUserTest'.date("Y-m-d");
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

        $eventResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для просмотра данного объекта',
                'errors' => [[]]
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
            'success' => false,
            'code' => 403,
            'message' => 'У вас нет доступа для просмотра данной страницы',
            'errors' => [[]]
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
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для просмотра данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($getEventResponse);
    }

    public function editEvent(ApiTester $I)
    {
        $eventName = self::eventName();
        $date = self::getDate();
        $id = $I->grabFromDatabase('objects', 'id', ['title' => $eventName]);
        $region = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);

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

        $eventEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для редактирования данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($eventEditResponse);
    }

    public function deleteEvent(ApiTester $I)
    {
        $eventName = 'eventUserTest'.date("Y-m-d");
        $id = $I->grabFromDatabase('pages', 'id', ['title' => $eventName]);

        $I->sendDELETE('/api/pages/'.$id);

        $eventResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для удаления данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($eventResponse);
    }
}