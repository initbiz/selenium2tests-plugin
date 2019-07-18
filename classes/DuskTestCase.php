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

    /**
     * Base url of application like: http://url.domain
     * @var string
     */
    public $baseUrl;

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
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless'
        ]);

        return RemoteWebDriver::create(
            'http://192.168.10.1:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
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

}
