<?php


namespace App\Tests\api\UserCurTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class PersonCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_CUR_USERNAME'), getenv('TEST_CUR_PASSWORD'));
    }

    public static function personName()
    {
        return 'personCurTest'.date("Y-m-d");
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

        $personId = $I->grabFromDatabase('pages', 'id', ['title' => $FIO]);

        $createPersonResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'redirectUrl' => '\/'.$personId
                ]
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
                'pagination' => []
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

        $typeId = $I->grabFromDatabase('object_types', 'id', ['title' => 'Персона']);

        $personEditResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'id' => $personObjectId,
                    'title' => 'new'.$FIO,
                    'type' => [
                        'id' => $typeId
                    ]
                ]
            ];

        $I->seeResponseContainsJson($personEditResponse);
    }

    public function deletePerson(ApiTester $I)
    {
        $FIO = self::personFIO();
        $personId = $I->grabFromDatabase('pages', 'id', ['title' => $FIO]);

        $I->sendDELETE('/api/pages/'.$personId);

        $personDeleteResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => 'Страница успешно удалена',
                'data' => []
            ];

        $I->seeResponseContainsJson($personDeleteResponse);
    }
}
