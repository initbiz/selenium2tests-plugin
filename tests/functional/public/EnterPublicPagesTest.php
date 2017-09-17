<?php

use InitBiz\Selenium2Tests\Classes\SeleniumTestCase;

class EnterPublicPagesTest extends SeleniumTestCase
{

    /** * @test * * @return void */
    public function guest_can_enter_public_pages()
    {
        $publicPages = [
            '/'
        ];

        foreach ($publicPages as $publicPage) {
            $this->visit($publicPage)->seePageIs($publicPage);
            //is redirected?
        }
    }
}
