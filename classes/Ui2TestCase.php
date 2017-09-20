<?php namespace InitBiz\Selenium2tests\Classes;

use PHPUnit_Extensions_Selenium2TestCase;
use SeleniumHelpers;
use OctoberSeleniumHelpers;

class Ui2TestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    use SeleniumHelpers;
    use OctoberSeleniumHelpers;

    protected $baseUrl;

    protected function setUp()
    {
        /*
         * Look for selenium configuration
         */
        if (file_exists($seleniumEnv = __DIR__.'/../selenium.php')) {
            require_once $seleniumEnv;
        }

        /*
         * Configure selenium
         */
        if (!defined('TEST_SELENIUM_URL')) {
            return $this->markTestSkipped('Selenium skipped');
        }

        $this->baseUrl = substr(TEST_SELENIUM_URL, 0, -1);
        $this->setBrowserUrl(TEST_SELENIUM_URL);

        if (defined('TEST_SELENIUM_HOST')) {
            $this->setHost(TEST_SELENIUM_HOST);
        }
        if (defined('TEST_SELENIUM_PORT')) {
            $this->setPort(TEST_SELENIUM_PORT);
        }
    }
}
