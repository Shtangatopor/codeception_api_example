<?php


namespace App\Tests\api\AdminTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class _07_OrganizationCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

    public static function organizationName()
    {
        return 'organizationTest'.date("Y-m-d");
    }

    public static function inn()
    {
        return '11111111';
    }

    public function createOrganization(ApiTester $I)
    {
        $Name = self::organizationName();
        $inn = self::inn();
        $personId = $I->grabFromDatabase('users', 'object_id', ['username' => getenv('TEST_ADMIN_USERNAME')]);
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
        $personId = $I->grabFromDatabase('users', 'object_id', ['username' => getenv('TEST_ADMIN_USERNAME')]);
        $organizationId = $I->grabFromDatabase('objects', 'id', ['title' => $organizationName]);
        $objectProjId = $I->grabFromDatabase('objects', 'id', ['title' => $organizationName]);
        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_ADMIN_USERNAME')]);
        $userName = $I->grabFromDatabase('users', 'username', ['id' => $userId]);

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

        $typeId = $I->grabFromDatabase('objects', 'type_id', ['title' => $organizationName.'11']);

        $organizationEditResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' =>
                    [[
                        'id' => $objectProjId,
                        'title' => 'Организация: 1111111111',
                        'type' => [
                            'id' => $typeId,
                            'title' => 'Организация',
                            'public' => true
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

        $I->seeResponseContainsJson($organizationEditResponse);
    }

    public function deleteOrganization(ApiTester $I)
    {
        $inn = self::inn();
        $organizationName = 'Организация: '.$inn;
        $organizationId = $I->grabFromDatabase('pages', 'id', ['title' => $organizationName]);

        $I->sendDELETE('/api/pages/'.$organizationId);

        $organizationDeleteResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => 'Страница успешно удалена',
                'data' => []
            ];

        $I->seeResponseContainsJson($organizationDeleteResponse);
    }
}