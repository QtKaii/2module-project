<?php

class unitTestUser
{
    private $db;
    public function __construct()
    {
        $test = new user([
            'username' => 'test1',
            'name' => 'test subject 1',
            'email' => 'test@gmail.com',
            'dob' => '11-11-1111',
            'password' => 'test', 
            'seller-toggle' => 0
        ]);
        $db = new SQLite3(__DIR__ . '/../db.sqlite', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $userDB = new userDB();
        $userDB->makeUser($test);
        error_log('Getters:');
        error_log($test->getUsername());
        error_log($test->getFullname());
        error_log($test->getEmail());
        error_log($test->getDob());
        error_log($test->getPassword());
        error_log($test->getIsSeller());
        error_log($test->getisAdmin());
        error_log('Now Setters:');
        $test->setUsername('Test1');
        $test->setFullname('Test Subject 1');
        $test->setEmail('test@test.com');
        $test->setDOB('01-01-0101');
        $test->getIsSeller();
        $test->setAdmin();
        error_log($test->getUsername());
        error_log($test->getFullname());
        error_log($test->getEmail());
        error_log($test->getDob());
        error_log($test->getPassword());
        error_log($test->getIsSeller());
        error_log($test->getisAdmin());
        error_log($test->getByUsername('test1'));

        $id= $test->getId($db);
        error_log($id?$id:'null');
        //var_dump($id);
    }
}

?>