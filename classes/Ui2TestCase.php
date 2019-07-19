<?php namespace Initbiz\Selenium2tests\Classes;

use Config;
use Laravel\Dusk\TestCase as BaseTestCase;
use Initbiz\Selenium2tests\Classes\Browser;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Initbiz\Selenium2tests\Classes\ElementResolver;

abstract class Ui2TestCase extends BaseTestCase
{
    public const DEFAULT_BROWSER = 'chrome';

    public const DEFAULT_HOST = '127.0.0.1';

    public const DEFAULT_PORT = '4444';

    /**
     * Browser
     *
     * @var Browser
     */
    public $browser;

    /**
     * Browser options
     *
     * @var array
     */
    public $browserOptions;
    
    /**
     * Selenium connection URI like http://127.0.0.1:4444/wd/hub
     *
     * @var string
     */
    protected $seleniumUri;
    
    /**
     * Driver
     *
     * @var RemoteWebDriver
     */
    public $driver;

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $this->configureSelenium();

        if ($this->browser = "chrome") {
            $browserOptions = (defined('TEST_SELENIUM_BROWSER_OPTIONS')) ? TEST_SELENIUM_BROWSER_OPTIONS : [];

            $options = (new ChromeOptions)->addArguments($browserOptions);

            $this->driver = RemoteWebDriver::create(
                $this->getSeleniumUri(),
                DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY,
                    $options
                )
            );
        }

        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

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

        return $app;
    }

    /**
     * Gets config from selenium.php file from plugin or project's root directory
     *
     * @return void
     */
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

        $browser = (defined('TEST_SELENIUM_BROWSER')) ? TEST_SELENIUM_BROWSER : self::DEFAULT_BROWSER;
        $host = (defined('TEST_SELENIUM_HOST')) ? TEST_SELENIUM_HOST : self::DEFAULT_HOST;
        $port = (defined('TEST_SELENIUM_PORT')) ? TEST_SELENIUM_PORT : self::DEFAULT_PORT;

        $this->initBrowser($browser);
        $this->initSeleniumUri($host, $port);
    }

    /**
     * Sets initial seleniumUri value
     *
     * @param string $host
     * @param string $port
     * @param string $suffix
     * @param string $prefix
     * @return void
     */
    public function initSeleniumUri(string $host = self::DEFAULT_HOST, string $port = self::DEFAULT_PORT, string $suffix = "/wd/hub", string $prefix = "http://")
    {
        $this->seleniumUri = $prefix . $host . ":" . $port . $suffix;
    }

    /**
     * Getter of seleniumUri
     *
     * @return string
     */
    public function getSeleniumUri():string
    {
        return $this->seleniumUri;
    }

    /**
     * Set initial browser
     *
     * @param string $browser
     * @return void
     */
    public function initBrowser(string $browser = self::DEFAULT_BROWSER)
    {
        $this->browser = $browser;
    }

    /**
     * {@inheritDoc}
     */
    protected function newBrowser($driver)
    {
        $resolver = new ElementResolver($this->driver);
        return new Browser($driver, $resolver);
    }
}
