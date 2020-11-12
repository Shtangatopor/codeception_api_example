<?php


namespace App\Tests\api\UserTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class CategoryCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_CUR_USERNAME'), getenv('TEST_CUR_PASSWORD'));
    }

    public static function categoryName()
    {
        return 'categoryCurTest'.date("Y-m-d");
    }

    public function createCategory(ApiTester $I)
    {
        $categoryName = self::categoryName();

        $categoryData = [
            'title' => $categoryName,
            'identifier' => $categoryName,
            'hideComments' => true,
            'hideTableOfContents' => true,
            'labels' => [],
            'parent' => null,
            'blocks' => [
                [
                    'title' => $categoryName,
                    'type' => 'html',
                    'sort' => 0,
                    'access_level' => 1,
                    'columns' => 0,
                    'value' => []
                ]
            ],
            'type' => 'category'
        ];

        $json = stripcslashes(json_encode($categoryData));

        $I->sendPOST('/api/pages', $json);

        $categoryId = $I->grabFromDatabase('pages', 'id', ['title' => $categoryName, 'identifier' => $categoryName]);
        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_CUR_USERNAME')]);

        $categoryResponse = [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' =>
                [
                    'id' => $categoryId,
                    'title' => $categoryName,
                    'creator' => [
                        'id' => $userId
                    ],
                    'tags' => [],
                    'identifier' => $categoryName,
                    'owners' => [[
                        'id' => $userId
                    ]],
                    'blocks' =>
                        [[
                            'value' => [],
                            'access_level' => 1,
                            'title' => $categoryName,
                            'type' => 'html'
                        ]],
                    'type' => 'category'
                ]
        ];

        $I->seeResponseContainsJson($categoryResponse);
    }

    public function getCategoryPages(ApiTester $I)
    {
        $categoryName = self::categoryName();
        $categoryId = $I->grabFromDatabase('pages', 'id', ['title' => $categoryName, 'identifier' => $categoryName]);

        $I->sendGET('/api/categories/'.$categoryId.'/pages');

        $categoryResponse = [
            "success" => true,
            "code" => 200,
            "message" => "",
            "data" => []
        ];

        $I->seeResponseContainsJson($categoryResponse);
    }

    public function editCategory(ApiTester $I)
    {
        $categoryName = self::categoryName();
        $categoryId = $I->grabFromDatabase('pages', 'id', ['title' => $categoryName, 'identifier' => $categoryName]);

        $categoryEditData = [
            'title' => $categoryName.'UP',
            'identifier' => $categoryName.'UP',
            'hideComments' => true,
            'hideTableOfContents' => true,
            'labels' => [],
            'parent' => null,
            'blocks' => [
                [
                    'title' => $categoryName,
                    'type' => 'html',
                    'sort' => 0,
                    'access_level' => 1,
                    'columns' => 0,
                    'value' => []
                ]
            ],
            'type' => 'category'
        ];

        $json = stripcslashes(json_encode($categoryEditData));

        $I->sendPUT('/api/pages/' . $categoryId, $json);

        $userId = $I->grabFromDatabase('users', 'id', ['username' => getenv('TEST_CUR_USERNAME')]);

        $categoryResponse = [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' =>
                [
                    'id' => $categoryId,
                    'title' => $categoryName.'UP',
                    'creator' => [
                        'id' => $userId
                    ],
                    'tags' => [],
                    'identifier' => $categoryName.'UP',
                    'owners' => [[
                        'id' => $userId
                    ]],
                    'blocks' =>
                        [[
                            'value' => [],
                            'access_level' => 1,
                            'title' => $categoryName,
                            'type' => 'html'
                        ]],
                    'type' => 'category'
                ]
        ];

        $I->seeResponseContainsJson($categoryResponse);
    }

    public function deleteCategory(ApiTester $I)
    {
        $categoryName = 'categoryCurTest'.date("Y-m-d").'UP';
        $categoryId = $I->grabFromDatabase('pages', 'id', ['title' => $categoryName, 'identifier' => $categoryName]);

        $I->sendDELETE('/api/pages/'.$categoryId);

        $categoryResponse =
            [
                'success' => true,
                'code' => 200,
                'message' => 'Страница успешно удалена',
                'data' => []
            ];

        $I->seeResponseContainsJson($categoryResponse);
    }
}
