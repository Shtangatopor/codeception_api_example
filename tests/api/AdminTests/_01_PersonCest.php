<?php


namespace App\Tests\api\AdminTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class _01_PersonCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

    public static function personName()
    {
        return 'personTest'.date("Y-m-d");
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

    public function getRegions(ApiTester $I)
    {
        //проект
        //401b107b-4667-4abd-ab81-de140d5b2ddb
        $objectType = $I->grabFromDatabase('object_types', 'id', ['title' => 'Проект']);
        $I->sendGET('/api/page/queries?&type='.$objectType.'&page=1&limit=25&view_type=table');

        $response = json_decode($I->grabResponse());
        $array = array();

        foreach ($response->objects as $object)
        {
            array_push($array, $object->title);
        }

        return $array;
    }

    public function getPersonByRegion(ApiTester $I)
    {
        //dcbefaeb-e819-4fa1-a13b-79ae99cbf15d
        $objectTypeId = $I->grabFromDatabase('forms_objects_fields', 'object_type_field_id', ['title' => 'Связан с проектом']);

        //персона
        //c630742c-0d48-4bb4-b315-3c88127ed090
        $objectType = $I->grabFromDatabase('object_types', 'id', ['title' => 'Персона']);

        $array = $this->getRegions($I);

        foreach ($array as $title) {
            $objectId = $I->grabFromDatabase('objects', 'id', ['title' => $title]);
            $I->sendGET('/api/page/queries?fields[' . $objectTypeId . '][]=' . $objectId . '&type=' . $objectType . '&page=1&limit=25&view_type=table');
            $I->makeHtmlSnapshot();
        }
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