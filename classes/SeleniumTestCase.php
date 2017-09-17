<?php namespace InitBiz\Selenium2Tests\Classes;

use Modelizer\Selenium\SeleniumTestCase as ModelizerSeleniumTestCase;
use Lang;

class SeleniumTestCase extends ModelizerSeleniumTestCase
{
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

    /**
     * Check a checkbox on the page that has name.
     *
     * @param $name
     *
     * @return $this
     */
    protected function check($name)
    {
        $this->findElement($name)->click();
        return $this;
    }

    //
    // OctoberCMS Helpers
    //

    protected function signInToBackend()
    {
        return $this->visit(TEST_SELENIUM_BACKEND_URL)
             ->type(TEST_SELENIUM_USER, 'login')
             ->type(TEST_SELENIUM_PASS, 'password')
             ->press('Zaloguj');
    }
}
