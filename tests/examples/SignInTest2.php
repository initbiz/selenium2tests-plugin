<?php

use Laravel\Dusk\Browser;
use Initbiz\Selenium2tests\Classes\DuskTestCase;

class SignInTest2 extends DuskTestCase
{
    /**
      * @test *
      * @return void
      */
    public function admin_can_sign_in_to_backend()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel');
        });

        // $this->visit('/backend/backend');
        
        // $this->seePageIs('/backend/backend');
        // $this->assertStringStartsWith('Dashboard', $this->title());
    }
}
