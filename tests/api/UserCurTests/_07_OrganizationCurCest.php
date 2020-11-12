<?php


namespace App\Tests\api\UserCurTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class OrganizationCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_CUR_USERNAME'), getenv('TEST_CUR_PASSWORD'));
    }

    public static function organizationName()
    {
        return 'organizationCurTest'.date("Y-m-d");
    }

    public static function inn()
    {
        return '11111111';
    }

    public function createOrganization(ApiTester $I)
    {
        $Name = self::organizationName();
        $inn = self::inn();
        $personId = $I->grabFromDatabase('users', 'object_id', ['username' => getenv('TEST_CUR_USERNAME')]);
        $formId = $I->grabFromDatabase('forms', 'id', ['title' => 'Организация']);

        $organizationData =
            [
                'kee2ycjy' =>
                    [
                        'Organizaciya/Organizaciya' =>
                            [[
                                'Adres' => '',
                                'Direktor' => $personId,
                                'Email' => '',
                                'Foto' => '',
                                'INN' => $inn,
                                'Nazvanie' => $Name,
                                'Sait' => '',
                                'Telefon' => ''
                            ]]
                    ]
            ];

        $json = stripcslashes(json_encode($organizationData));

        $I->sendPOST('/api/forms/'.$formId.'/send', $json);

        $inn = self::inn();
        $organizationName = 'Организация: '.$inn;
        $organizationId = $I->grabFromDatabase('pages', 'id', ['title' => $organizationName]);

        $organizationResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' => [
                    'redirectUrl' => '\/'.$organizationId
                ]
            ];

        $responseJson = stripcslashes(json_encode($organizationResponse));

        $I->seeResponseEquals($responseJson);
    }

    public function editOrganization(ApiTester $I)
    {
        $inn = self::inn();
        $organizationName = 'Организация: '.$inn;
        $personId = $I->grabFromDatabase('users', 'object_id', ['username' => getenv('TEST_CUR_USERNAME')]);
        $organizationId = $I->grabFromDatabase('objects', 'id', ['title' => $organizationName]);

        $organizationEditData =
            [
                'kee2ycjy' =>
                    [
                        'Organizaciya/Organizaciya' =>
                            [[
                                'Adres' => '',
                                'Direktor' => $personId,
                                'Email' => '',
                                'Foto' => '',
                                'INN' => $inn.'11',
                                'Nazvanie' => $organizationName,
                                'Sait' => '',
                                'Telefon' => ''
                            ]]
                    ]
            ];

        $json = stripcslashes(json_encode($organizationEditData));

        $I->sendPOST('/api/data_objects/'.$organizationId.'/save', $json);

        $organizationEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для редактирования данного типа объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($organizationEditResponse);
    }

    public function deleteOrganization(ApiTester $I)
    {
        $inn = self::inn();
        $organizationName = 'Организация: '.$inn;
        $organizationId = $I->grabFromDatabase('pages', 'id', ['title' => $organizationName]);

        $I->sendDELETE('/api/pages/'.$organizationId);

        $organizationResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для редактирования данного типа объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($organizationResponse);
    }
}