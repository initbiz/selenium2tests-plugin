<?php namespace Initbiz\Selenium2tests\Tests\Examples;

use Initbiz\Selenium2tests\Classes\Browser;
use Initbiz\Selenium2tests\Classes\Ui2TestCase;

class SignInTest extends Ui2TestCase
{
    /**
      * @test *
      * @return void
      */
    public function admin_can_sign_in_to_backend()
    {
        $this->browse(function (Browser $browser) {
            $browser->signInToBackend()
                    ->assertPathIs('/backend/backend');
        });

    }
}
