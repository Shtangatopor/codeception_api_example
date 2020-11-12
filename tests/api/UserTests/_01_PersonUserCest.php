<?php


namespace App\Tests\api\UserTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use Codeception\Example;

class PersonUserCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_USER_USERNAME'), getenv('TEST_USER_PASSWORD'));
    }

    public static function personName()
    {
        return 'personUserTest'.date("Y-m-d");
    }

    public static function personFIO()
    {
        return 'test test test';
    }

    public function createPerson(ApiTester $I)
    {
        $personName = self::personName();
        $FIO = self::personFIO();
        $id = $I->grabFromDatabase('forms', 'id', ['title' => 'Персона']);

        $personData =
            [
                'ke9o7jow' =>
                    [
                        'Persona/Persona' =>
                            [[
                                'Doljnost' =>  'Программист 2 кат.',
                                'Email' =>
                                    [
                                        'test@test.ru'
                                    ],
                                'FIO' => $FIO,
                                'Foto' => '',
                                'Imya_polzovatelya' => $personName,
                                'Rabotaet_po_regionu' => [],
                                'Rol_CUR' => '',
                                'Telefon' =>
                                    [
                                        '89999999999'
                                    ],
                                'Telegram' =>  '@test_test',
                                'Telegram_bot_ID' => ''
                            ]]
                    ]
            ];

        $json = stripcslashes(json_encode($personData));

        $I->sendPOST('/api/forms/'.$id.'/send', $json);

        $createPersonResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для просмотра данного объекта',
                'errors' => [[]]
            ];

        $responseJson = stripcslashes(json_encode($createPersonResponse));

        $I->seeResponseEquals($responseJson);
    }

    public function getPersonByRegion(ApiTester $I)
    {
        $objectTypeId = $I->grabFromDatabase('forms_objects_fields', 'object_type_field_id', ['title' => 'Связан с проектом']);
        $objectId = $I->grabFromDatabase('objects', 'id', ['title' => 'Тверь']);
        $objectTypesId = $I->grabFromDatabase('object_types', 'id', ['title' => 'Персона']);

        $I->sendGET('/api/page/queries?fields['.$objectTypeId.'][]='.$objectId.'&type='.$objectTypesId.'&page=1&limit=25&view_type=table');

        $getPersonResponse =
            [
                'objects' => [],
                'pagination' => [],
                'count' => 25,
                'totalCount' => 0
            ];

        $I->seeResponseContainsJson($getPersonResponse);
    }

    public function editPerson(ApiTester $I)
    {
        $personName = self::personName();
        $FIO = self::personFIO();
        $personObjectId = $I->grabFromDatabase('objects', 'id', ['title' => $FIO]);

        $personEditData = [
            'ke9o7jow' =>
                [
                    'Persona/Persona' =>
                        [[
                            'Doljnost' =>  'Программист 2 кат.',
                            'Email' =>
                                [
                                    'test@test.ru'
                                ],
                            'FIO' => 'new'.$FIO,
                            'Foto' => '',
                            'Imya_polzovatelya' => $personName,
                            'Rabotaet_po_regionu' => [],
                            'Rol_CUR' => '',
                            'Telefon' =>
                                [
                                    '89999999999'
                                ],
                            'Telegram' =>  '@test_test',
                            'Telegram_bot_ID' => ''
                        ]]
                ]
        ];

        $json = stripcslashes(json_encode($personEditData));

        $I->sendPOST('/api/data_objects/'.$personObjectId.'/save', $json);

        $personEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для редактирования данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($personEditResponse);
    }

    public function deletePerson(ApiTester $I)
    {
        $FIO = self::personFIO();
        $personId = $I->grabFromDatabase('pages', 'id', ['title' => $FIO]);

        $I->sendDELETE('/api/pages/'.$personId);

        $personResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для удаления данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($personResponse);
    }
}