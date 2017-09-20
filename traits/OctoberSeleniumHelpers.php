<?php

/**
 * Trait with Selenium 2 helper methods for October
 */

trait OctoberSeleniumHelpers
{
    protected function signInToBackend()
    {
        return $this->visit(TEST_SELENIUM_BACKEND_URL)
             ->type(TEST_SELENIUM_USER, 'login')
             ->type(TEST_SELENIUM_PASS, 'password')
             ->click("//button[@type='submit']");
    }
}
