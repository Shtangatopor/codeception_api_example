<?php


namespace App\Tests\api\UserTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class TaskUserCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_USER_USERNAME'), getenv('TEST_USER_PASSWORD'));
    }

    public static function taskName()
    {
        return 'taskUserTest'.date("Y-m-d");
    }

    public function createTask(ApiTester $I)
    {
        $taskName = self::taskName();
        $id = $I->grabFromDatabase('forms', 'id', ['title' => 'Задача']);
        $reqion = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);

        $taskData =
            [
                'ke4d3yvm' =>
                    [
                        'Zadacha/Zadacha' =>
                            [[
                                'Data_nachala' => '',
                                'Etap' => '',
                                'Data_okonchaniya_zadachi' => '',
                                'Fail' => '',
                                'Opisanie_zadachi' => '',
                                'Otvetstvennyi' => '',
                                'Soispolniteli' => '',
                                'Prioritet' => '',
                                'Region' => $reqion,
                                'Roditelskaya_zadacha' => '',
                                'Status' => 'Новая',
                                'Treker' => '',
                                'Zadacha' => $taskName
                            ]]
                    ]
            ];

        $json = stripcslashes(json_encode($taskData));

        $I->sendPOST('/api/forms/'.$id.'/send', $json);

        $taskResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для просмотра данного объекта',
                'errors' => [[]]
            ];

        $responseJson = stripcslashes(json_encode($taskResponse));

        $I->seeResponseEquals($responseJson);
    }

    public function editTaskStatus(ApiTester $I)
    {
        $taskName = self::taskName();
        $taskId = $I->grabFromDatabase('objects', 'id', ['title' => $taskName]);
        $reqion = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);
        $objectProjId = $I->grabFromDatabase('objects', 'id', ['title' => $taskName]);
        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_USER_USERNAME')]);
        $userName = $I->grabFromDatabase('users', 'username', ['id' => $userId]);

        $editData = [
            'ke4d3yvm' =>
                [
                    'Zadacha/Zadacha' =>
                        [[
                            'Data_nachala' => '',
                            'Etap' => '',
                            'Data_okonchaniya_zadachi' => '',
                            'Fail' => '',
                            'Opisanie_zadachi' => '',
                            'Otvetstvennyi' => '',
                            'Soispolniteli' => '',
                            'Prioritet' => '',
                            'Region' => $reqion,
                            'Roditelskaya_zadacha' => '',
                            'Status' => 'В работе',
                            'Treker' => '',
                            'Zadacha' => $taskName
                        ]]
                ]
        ];

        $json = stripcslashes(json_encode($editData));

        $I->sendPOST('/api/data_objects/'.$taskId.'/save', $json);

        $taskEditResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => '',
                'data' =>
                    [
                        'redirectUrl' => '\/'.$taskId
                    ]
            ];

        $I->seeResponseContainsJson($taskEditResponse);
    }

    public function updateTask(ApiTester $I)
    {
        $taskName = self::taskName();
        $taskId = $I->grabFromDatabase('objects', 'id', ['title' => $taskName]);
        $reqion = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);

        $editData = [
            'ke4d3yvm' =>
                [
                    'Zadacha/Zadacha' =>
                        [[
                            'Data_nachala' => '',
                            'Etap' => '',
                            'Data_okonchaniya_zadachi' => '',
                            'Fail' => '',
                            'Opisanie_zadachi' => '',
                            'Otvetstvennyi' => '',
                            'Soispolniteli' => '',
                            'Prioritet' => '',
                            'Region' => $reqion,
                            'Roditelskaya_zadacha' => '',
                            'Status' => 'В работе',
                            'Treker' => '',
                            'Zadacha' => $taskName.'UP'
                        ]]
                ]
        ];

        $json = stripcslashes(json_encode($editData));

        $I->sendPOST('/api/data_objects/'.$taskId.'/save', $json);

        $taskEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для редактирования данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($taskEditResponse);
    }

    public function getTaskTest(ApiTester $I)
    {
        $taskType = $I->grabFromDatabase('object_types', 'id', ['title' => 'Задача']);
        $projectId = $I->grabFromDatabase('objects', 'id', ['title' => '!TEST SD']);
        $objectTypeId = $I->grabFromDatabase('forms_objects_fields', 'object_type_field_id', ['title' => 'Регион']);

        $I->sendGET('/api/page/queries?fields['.$objectTypeId.'][]='.$projectId.'&type='.$taskType.'&page=1&limit=25&view_type=table');
        $I->seeResponseCodeIs('200');

        $getTaskResponse =
            [
                'objects' => [],
                'pagination' => []
            ];

        $I->seeResponseContainsJson($getTaskResponse);
    }

//    # создание задач региону
//    public function createtaskByRegion(ApiTester $I)
//    {
//        $taskName= self::taskName();
//        $id = $I->grabFromDatabase('forms', 'id', ['title' => 'Задача']);
//        $projectId = $I->grabFromDatabase('objects', 'id', ['title' => 'projectTest'.date("Y-m-d")]);
//
//        $taskData =
//            [
//                'ke4d3yvm' =>
//                    [
//                        'Zadacha/Zadacha' =>
//                            [[
//                                'Data_okonchaniya_zadachi' => '',
//                                'Fail' => '',
//                                'Opisanie_zadachi' => '',
//                                'Otvetstvennyi' => '',
//                                'Prioritet' => '',
//                                'project' => $projectId,
//                                'Roditelskaya_zadacha' => '',
//                                'Status' => 'Новая',
//                                'Zadacha' => $taskName
//                            ]]
//                    ]
//            ];
//
//        $json = stripcslashes(json_encode($taskData));
//
//        $I->sendPOST('/api/forms/'.$id.'/send', $json);
//        $I->seeResponseCodeIs('403');
//
//        $I->makeHtmlSnapshot();
//    }

    public function deleteTasks(ApiTester $I)
    {
        $taskName = 'taskUserTest'.date("Y-m-d");
        $taskId = $I->grabFromDatabase('pages', 'id', ['title' => $taskName]);

        $I->sendDELETE('/api/pages/'.$taskId);

        $taskResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для удаления данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($taskResponse);
    }
}