<?php namespace Initbiz\Selenium2tests\Classes;

use Config;
use Laravel\Dusk\Browser as DuskBrowser;

class Browser extends DuskBrowser
{
    use \Initbiz\Selenium2tests\Traits\DuskAdapter;
    use \Initbiz\Selenium2tests\Traits\SeleniumHelpers;
    use \Initbiz\Selenium2tests\Traits\OctoberSeleniumHelpers;

    public $backendUri;

    public $backendAdmin;

    public function __construct($driver, $resolver = null)
    {
        parent::__construct($driver, $resolver);

        $this->configBrowser();
    }

    public function configBrowser()
    {
        $this->backendUri = Config::get('cms.backendUri');

        if (file_exists($seleniumEnv = __DIR__.'/../selenium.php')) {
            require_once $seleniumEnv;
        } elseif (file_exists($seleniumEnv = __DIR__.'/../../../../selenium.php')) {
            require_once $seleniumEnv;
        }

        if (defined('TEST_SELENIUM_USER')) {
            $this->backendAdmin['username'] = TEST_SELENIUM_USER;
        } else {
            $this->backendAdmin['username'] = "admin";
        }

        if (defined('TEST_SELENIUM_PASS')) {
            $this->backendAdmin['password'] = TEST_SELENIUM_PASS;
        } else {
            $this->backendAdmin['password'] = "admin";
        }
    }
}
