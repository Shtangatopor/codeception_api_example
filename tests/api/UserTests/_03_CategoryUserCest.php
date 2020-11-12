<?php


namespace App\Tests\api\UserTests;


use App\Tests\ApiTester;
use App\Tests\Helper\Api;

class CategoryUserCest
{
    public function _before(Api $api)
    {
        $api->authorization(getenv('TEST_USER_USERNAME'), getenv('TEST_USER_PASSWORD'));
    }

    public static function categoryName()
    {
        return 'categoryUserTest'.date("Y-m-d");
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

        $categoryResponse = [
            'success' => false,
            'code' => 403,
            'message' => 'У вас нет прав для просмотра данного объекта',
            'errors' => [[]]
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

        $categoryEditResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет прав для редактирования данного объекта',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($categoryEditResponse);
    }

    public function deleteCategory(ApiTester $I)
    {
        $categoryName = 'categoryUserTest'.date("Y-m-d").'UP';
        $categoryId = $I->grabFromDatabase('pages', 'id', ['title' => $categoryName, 'identifier' => $categoryName]);

        $I->sendDELETE('/api/pages/'.$categoryId);

        $categoryResponse =
            [
                'success' => false,
                'code' => 403,
                'message' => 'У вас нет доступа для удаления данной страницы',
                'errors' => [[]]
            ];

        $I->seeResponseContainsJson($categoryResponse);
    }
}
