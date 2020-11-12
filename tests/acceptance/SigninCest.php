<?php namespace App\Tests;

use App\Tests\Page\Acceptance\Login;

class SigninCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function login(Login $LoginPage)
    {
        $LoginPage->login(getenv('TEST_ADMIN_USERNAME'), getenv('TEST_ADMIN_PASSWORD'));
    }

}
