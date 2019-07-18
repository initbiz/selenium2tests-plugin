<?php namespace Initbiz\Selenium2tests\Classes;

use Config;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    // use CreatesApplication;
    // use \Initbiz\Selenium2tests\Traits\SeleniumHelpers;
    // use \Initbiz\Selenium2tests\Traits\OctoberSeleniumHelpers;

    public $baseUrl;
    public $backendUrl;
    public $browser;
    public $seleniumUri;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        // static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $this->configureSelenium();

        if ($this->browser = "chrome") {
            $options = (new ChromeOptions)->addArguments(TEST_SELENIUM_BROWSER_OPTIONS);

            return RemoteWebDriver::create(
                'http://' . $this->seleniumUri . '/wd/hub',
                DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY,
                    $options
                )
            );
        }
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['cache']->setDefaultDriver('array');
        $app->setLocale('en');

        /*
         * Store database in memory by default, if not specified otherwise
         */
        $dbConnection = 'sqlite';

        $dbConnections = [];
        $dbConnections['sqlite'] = [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => ''
        ];

        if (env('APP_ENV') === 'testing' && Config::get('database.useConfigForTesting', false)) {
            $dbConnection = Config::get('database.default', 'sqlite');

            $dbConnections[$dbConnection] = Config::get('database.connections' . $dbConnection, $dbConnections['sqlite']);
        }

        $app['config']->set('database.default', $dbConnection);
        $app['config']->set('database.connections.' . $dbConnection, $dbConnections[$dbConnection]);

        /*
         * Modify the plugin path away from the test context
         */
        $app->setPluginsPath(realpath(base_path().Config::get('cms.pluginsPath')));

        return $app;
    }

    protected function configureSelenium()
    {
        /*
         * Look for selenium configuration
         */
        if (file_exists($seleniumEnv = __DIR__.'/../selenium.php')) {
            require_once $seleniumEnv;
        } elseif (file_exists($seleniumEnv = __DIR__.'/../../../../selenium.php')) {
            require_once $seleniumEnv;
        }

        if (defined('TEST_SELENIUM_BROWSER')) {
            $this->browser = TEST_SELENIUM_BROWSER;
        } else {
            $this->browser = 'chrome';
        }

        $this->baseUrl = Config::get('app.url');
        $this->backendUrl = Config::get('cms.backendUri');

        $this->seleniumUri = "";

        if (defined('TEST_SELENIUM_HOST')) {
            $this->seleniumUri .= TEST_SELENIUM_HOST;
        } else {
            $this->seleniumUri .= "127.0.0.1";
        }

        if (defined('TEST_SELENIUM_PORT')) {
            $this->seleniumUri .= ":" . TEST_SELENIUM_PORT;
        } else {
            $this->seleniumUri .= ":4444";
        }
    }
}
