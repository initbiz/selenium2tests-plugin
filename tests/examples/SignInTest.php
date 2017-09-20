<?php

use InitBiz\Selenium2tests\Classes\Ui2TestCase;

class SignInTest extends Ui2TestCase
{
    /**
      * @test *
      * @return void
      */
    public function admin_can_sign_in_to_backend()
    {
        $this->signInToBackend();
    }
}
