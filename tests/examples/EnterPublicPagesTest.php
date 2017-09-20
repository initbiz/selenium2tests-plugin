<?php

use InitBiz\Selenium2tests\Classes\Ui2TestCase;

class EnterPublicPagesTest extends Ui2TestCase
{

    /** * @test * * @return void */
    public function guest_can_enter_public_pages()
    {
        $publicPages = [
            '/'
        ];

        foreach ($publicPages as $publicPage) {
            $this->visit($publicPage)->seePageIs($publicPage);
        }
    }
}
