<?php namespace Initbiz\Selenium2tests\Classes;

use Laravel\Dusk\TestCase as BaseTestCase;
use Initbiz\Selenium2tests\Classes\Browser;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Initbiz\Selenium2tests\Classes\ElementResolver;

abstract class Ui2TestCase extends BaseTestCase
{
    /**
     * Browser
     *
     * @var Browser
     */
    public $browser;
    
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
            $options = (new ChromeOptions)->addArguments(TEST_SELENIUM_BROWSER_OPTIONS);

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

        if (defined('TEST_SELENIUM_BROWSER')) {
            $this->browser = TEST_SELENIUM_BROWSER;
        } else {
            $this->browser = 'chrome';
        }

        $this->initSeleniumUri(TEST_SELENIUM_HOST, TEST_SELENIUM_PORT);
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
    public function initSeleniumUri(string $host = "127.0.0.1", string $port = "4444", string $suffix = "/wd/hub", string $prefix = "http://")
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
     * {@inheritDoc}
     */
    protected function newBrowser($driver)
    {
        $resolver = new ElementResolver($this->driver);
        return new Browser($driver, $resolver);
    }
}
