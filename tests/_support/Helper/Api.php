<?php

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Tests\AcceptanceTester;
use App\Tests\ApiTester;

class Api extends \Codeception\Module
{
    /**
     * @var \App\Tests\ApiTester;
     */
    protected $apiTester;

    protected $acceptanceTester;

    public function __construct(ApiTester $api, AcceptanceTester $I)
    {
        $this->apiTester = $api;
        $this->acceptanceTester = $I;
    }

    public function authorization($username, $password)
    {
        $api = $this->apiTester;
        $this->$username = $username;
        $this->$password = $password;

        $api->amOnPage('/login');
        $csrf_token = $api->grabAttributeFrom('.login-form > input:nth-child(5)', 'value');

        $api->sendPOST('/login', [
            'username' => $username,
            'password' => $password,
            '_remember_me' => 'on',
            '_csrf_token' => $csrf_token
        ]);
    }
}
