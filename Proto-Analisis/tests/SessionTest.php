<?php

use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testSessionDestroy()
    {

        $_SESSION['test_key'] = 'test_value';


        session_start();
        session_destroy();


        $this->assertEmpty($_SESSION);
    }

    public function testRedirectAfterSessionDestroy()
    {

        $_SESSION['test_key'] = 'test_value';
    

        session_start();
        session_destroy();
    

        $this->assertTrue(empty($_SERVER['HTTP_LOCATION']));
    }
    
}
