<?php

namespace App\Tests\api\AdminTests;

use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use DateTime;

class _02_PageCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

    public static function pageName()
    {
        return 'pageTests'.date("Y-m-d");
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

        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName]);
        $creatorId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_ADMIN_USERNAME')]);

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
                    "access_level" => 1,
                    "title" => $pageName,
                    "type" => "html"
                ]],
                "type" => "page"
            ]
        ];

        $I->seeResponseContainsJson($pageResponse);
    }

    public function getPageTest(ApiTester $I)
    {
        $pageName = self::pageName();

        $I->sendGET('/api/pages/get/'.$pageName);

        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName]);
        $creatorId = $I->grabFromDatabase('users', 'id', [getenv('TEST_ADMIN_USERNAME')]);

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

        $responseJson = stripcslashes(json_encode($response));

        $I->seeResponseEquals($responseJson);
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

        $creatorId = $I->grabFromDatabase('users', 'id', [getenv('TEST_ADMIN_USERNAME')]);

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
                "title" => $pageName.'UP',
                "identifier" => $pageName.'UP',
                "owners" => [[
                    "id" => $creatorId
                ]],
                "blocks" => [[
                    "value" => [],
                    "access_level" => 1,
                    "title" => $pageName,
                    "type" => "html"
                ]],
                "type" => "page"
            ]
        ];

        $I->seeResponseContainsJson($pageResponse);
    }

    public function deletePage(ApiTester $I)
    {
        $pageName = 'pageTests'.date("Y-m-d").'UP';
        $pageId = $I->grabFromDatabase('pages', 'id', ['title' => $pageName, 'identifier' => $pageName]);

        $I->sendDELETE('/api/pages/'.$pageId);

        $pageResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => 'Страница успешно удалена',
                'data' => []
            ];

        $I->seeResponseContainsJson($pageResponse);
    }
}
