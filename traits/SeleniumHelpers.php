<?php namespace Initbiz\Selenium2tests\Traits;

/**
 * Trait with Selenium 2 helper methods
 * Most of methods here are based on Modeliser/Laravel-Selenium
 */
trait SeleniumHelpers
{
    public function isElementPresent($target)
    {
        try {
            $this->findElement($target);
        } catch (\Exception $e) {
            return false;
        }

        $displayed = $this->findElement($target)->isDisplayed();

        return $displayed;
    }


    /**
     *  Method waiting for element to be visible on page
     *  It is changed version of method from UiTestCase from OctoberCMS
      *
      * @param $target
      * @param 60 $timeout
      *
      * @throws Exception
      *
      * @return $this
     */
    public function waitForElementPresent($target, $timeout = 60)
    {
        for ($second = 0; ; $second++) {
            if ($second >= $timeout) {
                throw new \Exception('Timeout');
            }

            try {
                if ($this->isElementPresent($target)) {
                    break;
                }
            } catch (Exception $e) {
            }

            sleep(1);
        }
        return $this;
    }

    /**
     *  Method waiting for element to hide from page
     *  It is changed version of method from UiTestCase from OctoberCMS
      *
      * @param $target
      * @param 60 $timeout
      *
      * @throws Exception
      *
      * @return $this
     */
    public function waitForElementNotPresent($target, $timeout = 60)
    {
        for ($second = 0; ; $second++) {
            if ($second >= $timeout) {
                throw new \Exception('Timeout');
            }

            try {
                if (!$this->isElementPresent($target)) {
                    break;
                }
            } catch (Exception $e) {
            }

            sleep(1);
        }
        return $this;
    }

    public $waitForTypes = ['Id', 'CssSelector', 'ClassName', 'XPath'];
    /**
     * Generalized WaitsFor function to wait for a specific element with value
     * by the type passed.
     *
     * @param $type The type of selector we are using to wait for
     * @param $value The value of the selector we are using
     * @param $timeout
     *
     * @throws \Exception
     */
    public function waitForElement($type, $value, $timeout)
    {
        if (!in_array($type, $this->waitForTypes)) {
            throw new \Exception('Invalid wait for element type to wait for on the page');
        }
        $webdriver = $this;
        $this->waitUntil(function () use ($type, $value, $webdriver) {
            $function = 'by'.$type;
            try {
                $webdriver->$function($value);
                return true;
            } catch (\Exception $e) {
                return; // haven't found the element yet
            }
        }, $timeout);
    }
    /**
     * Helper method to wait for an element with the specified class.
     *
     * @param $class
     * @param int $timeout
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function waitForElementsWithClass($class, $timeout = 2000)
    {
        try {
            $this->waitForElement('ClassName', $class, $timeout);
        } catch (\Exception $e) {
            throw new \Exception("Can't find an element with the class name of "
                .$class.' within the time period of '.$timeout.' miliseconds');
        }
        return $this;
    }
    /**
     * Helper method to wait for an element with the specified id.
     *
     * @param $id
     * @param int $timeout
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function waitForElementWithId($id, $timeout = 2000)
    {
        try {
            $this->waitForElement('Id', $id, $timeout);
        } catch (\Exception $e) {
            throw new \Exception("Can't find an element with an ID of "
                .$id.' within the time period of '.$timeout.' miliseconds');
        }
        return $this;
    }
    /**
     * Helper method to wait for an element with the specified xpath.
     *
     * @param $xpath
     * @param int $timeout
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function waitForElementWithXPath($xpath, $timeout = 2000)
    {
        try {
            $this->waitForElement('XPath', $xpath, $timeout);
        } catch (\Exception $e) {
            throw new \Exception("Can't find an element with an XPath of "
                .$xpath.' within the time period of '.$timeout.' miliseconds');
        }
        return $this;
    }

    /**
     * Click the label with the value in it
     * @param  $value substring in label to click
     * @return $this
     */
    public function clickLabel($value)
    {
        $this->findElement($value, "//label[contains(., '{$value}')]")
             ->click();
        return $this;
    }

}
