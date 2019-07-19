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
