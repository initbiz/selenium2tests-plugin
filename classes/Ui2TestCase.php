<?php namespace Initbiz\Selenium2tests\Classes;

use PHPUnit_Extensions_Selenium2TestCase;
use Initbiz\Selenium2tests\Traits\SeleniumHelpers;
use Initbiz\Selenium2tests\Traits\OctoberSeleniumHelpers;

class Ui2TestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    use SeleniumHelpers;
    use OctoberSeleniumHelpers;

    /**
     * Base url of application like: http://url.domain
     * @var string
     */
    public $baseUrl;

    /**
     * BaseUrl + backend URL set in ENV
     * @var string
     */
    public $backendUrl;

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

        if (defined('TEST_SELENIUM_BROWSER')) {
            $this->setBrowser(TEST_SELENIUM_BROWSER);
        } else {
            $this->setBrowser('chrome');
        }

        $this->baseUrl = substr(TEST_SELENIUM_URL, 0, -1);
        $this->setBrowserUrl(TEST_SELENIUM_URL);

        $this->backendUrl = $this->baseUrl . '/' . TEST_SELENIUM_BACKEND_URL;

        if (defined('TEST_SELENIUM_HOST')) {
            $this->setHost(TEST_SELENIUM_HOST);
        }

        if (defined('TEST_SELENIUM_PORT')) {
            $this->setPort(TEST_SELENIUM_PORT);
        }
        $this->beforeTest();
    }


    protected function tearDown()
    {
        $this->afterTest();
        parent::tearDown();
    }

    /**
     * Method to be overridden by children and will be started before every test
     * @return void
     */
    protected function beforeTest()
    {
    }

    /**
     * Method to be overridden by children and will be started before parent::tearDown
     * @return void
     */
    protected function afterTest()
    {
    }
}
