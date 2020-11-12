<?php

namespace App\Tests\api\UserTests;

use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class PageUserCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_USER_USERNAME'), getenv('TEST_USER_PASSWORD'));
    }

    public static function pageName()
    {
        return 'pageUserTests'.date("Y-m-d");
    }

    public function createPage(ApiTester $I)
    {
        $pageName = self::pageName();

        $pageData =
            [
                'title' => $pageName,
                'identifier' => $pageName,
                'hideComments' => true,
                'hideTableOfContents' => true,
                'labels' => [],
                'parent' => null,
                'blocks' => [
                    [
                        'title' => $pageName,
                        'type' => 'html',
                        'sort' => 0,
                        'access_level' => 1,
                        'value' => []
                    ]
                ],
                'type' => 'page'
            ];

        $json = stripcslashes(json_encode($pageData));

        $I->sendPOST('/api/pages', $json);

        $pageResponse = [
            'success' => false,
            'code' => 403,
            'message' => 'У вас нет прав для просмотра данного объекта',
            'errors' => [[]]
        ];

        $I->seeResponseContainsJson($pageResponse);
    }

    public function getPageTest(ApiTester $I)
    {
        $pageName = self::pageName();

        $I->sendGET('/api/pages/get/'.$pageName);
        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName]);
        $creatorId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_USER_USERNAME')]);

        $pageResponse = [
            "success" => true,
            "code" => 200,
            "message" => "",
            'data' => [
                "id" => $pageId,
                "creator" => [
                    "id" => $creatorId
                ],
                "tags" => [],
                "title" => $pageName,
                "identifier" => $pageName,
                "owners" => [[
                    "id" => $creatorId
                ]],
                "blocks" => [[
                    "value" => [],
                    "title" => $pageName,
                ]],
            ]
        ];

        $I->seeResponseContainsJson($pageResponse);
    }

    public function getLinkedPageByPageId(ApiTester $I)
    {
        $pageName = self::pageName();
        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName]);

        $I->sendGET('/api/pages/'.$pageId.'/linked/to_this');

        $response = [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' => [
                "columnLabels" => [],
                "filters" => [],
                "filterValues" => [
                    "creator" => [],
                    "category" => [],
                    "createdAt" => [],
                    "link" => []
                ],
                "items" => []
            ]
        ];

        $I->seeResponseContainsJson($response);
    }

    public function getCommentsToPage(ApiTester $I)
    {
        $pageName = self::pageName();
        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName]);

        $I->sendGET('/api/pages/get/'.$pageId.'/comments');

        $response = [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' => [],
        ];

        $I->seeResponseContainsJson($response);
    }

    public function updatePage(ApiTester $I)
    {
        $pageName = self::pageName();
        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName, 'identifier' => $pageName]);

        $pageEditData =
            [
                'title' => $pageName.'UP',
                'identifier' => $pageName.'UP',
                'hideComments' => true,
                'hideTableOfContents' => true,
                'labels' => [],
                'parent' => null,
                'blocks' => [
                    [
                        'title' => $pageName,
                        'type' => 'html',
                        'sort' => 0,
                        'access_level' => 1,
                        'value' => []
                    ]
                ],
                'type' => 'page'
            ];

        $json = stripcslashes(json_encode($pageEditData));

        $I->sendPUT('/api/pages/'.$pageId, $json);

        $pageEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для редактирования данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($pageEditResponse);
    }

    public function deletePage(ApiTester $I)
    {
        $pageName = 'pageUserTests'.date("Y-m-d").'UP';
        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName, 'identifier' => $pageName]);

        $I->sendDELETE('/api/pages/'.$pageId);

        $pageResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для удаления данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($pageResponse);
    }
}
