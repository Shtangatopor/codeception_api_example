<?php

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class DbHelper extends \Codeception\Module
{


    /**
     * Additional methods for DB module
     *
     * Save this file as DbHelper.php in _support folder
     * Enable DbHelper in your suite.yml file
     * Execute `codeception build` to integrate this class in your codeception
     */


    /**
     * Function to run more complex queries
     * example
     * @param $query
     * @return mixed
     * @throws \Codeception\Exception\ModuleException
     */
    public function runSqlQuery($query)
    {
        $dbh = $this->getModule("Db")->dbh;
        $result = $dbh->query($query);
        return $result->fetchAll();
    }
}