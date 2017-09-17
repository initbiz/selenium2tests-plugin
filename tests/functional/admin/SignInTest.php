<?php

use InitBiz\Selenium2Tests\Classes\SeleniumTestCase;

class SignInTest extends SeleniumTestCase
{
    use SignIn;
    /**
      * @test *
      * @return void
      */
    public function admin_can_sign_in_to_backend()
    {
        $this->signInToBackend();
    }
}
