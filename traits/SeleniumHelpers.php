<?php namespace Initbiz\Selenium2tests\Traits;

/**
 * Trait with Selenium 2 helper methods
 * Most of methods here are based on Modeliser/Laravel-Selenium
 */
trait SeleniumHelpers
{
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

    /**
     * Visit a URL within the browser.
     *
     * @param $path
     *
     * @return $this
     */
    protected function visit($path)
    {
        $this->url($path);
        return $this;
    }

    /**
     * Scroll the page in the x-axis by the amount specified.
     *
     * @param $amount Positive values go down the page, negative values go up the page
     *
     * @return $this
     */
    protected function scroll($amount)
    {
        $this->execute([
            'script' => 'window.scrollBy(0, '.$amount.')',
            'args'   => [],
        ]);
        return $this;
    }
    /**
     * Type a value into a form input by that inputs name.
     *
     * @param $value
     * @param $name
     * @param bool $clear Whether or not to clear the input first on say an edit form
     *
     * @return $this
     */
    protected function type($value, $name, $clear = false)
    {
        $element = $this->findElement($name);
        if ($clear) {
            $element->clear();
        }
        $element->value($value);
        return $this;
    }
    /**
         * Press a button on the page that contains text.
         *
         * @param $text
         *
         * @return $this
         */
    protected function press($text)
    {
        $this->findElement($text, "//button[contains(., '{$text}')]")->click();
        return $this;
    }

    /**
     * Assert that we see text within the specified tag
     * Defaults to the body tag.
     *
     * @param $text
     * @param string $tag
     *
     * @return $this
     */
    protected function see($text, $tag = 'body')
    {
        $this->assertContains($text, $this->byTag($tag)->text());
        return $this;
    }

    /**
     * User should not be able to see element.
     *
     * @param $text
     * @param string $tag
     */
    protected function notSee($text, $tag = 'body')
    {
        $this->assertNotContains($text, $this->byTag($tag)->text());
        return $this;
    }

    /**
    * "Select" a drop-down field.
    *
    * @param $element
    * @param $name
    */
    protected function select($element, $value)
    {
        $this->findElement($element)->value($value);
        return $this;
    }

    /**
    * Assert the page is at the path that you specified.
    *
    * @param $path
    *
    * @return $this
    */
    protected function seePageIs($path)
    {
        $this->assertEquals($this->baseUrl . $path, $this->url());
        return $this;
    }

    protected function hold($seconds)
    {
        sleep($seconds);
        return $this;
    }

    /**
     * Function to type information as an array
     * The key of the array specifies the input name.
     *
     * @param $information
     * @param $clear
     *
     * @return $this
     */
    protected function typeInformation($information, $clear = false)
    {
        foreach ($information as $element => $item) {
            $this->type($item, $element, $clear);
        }
        return $this;
    }
    protected function submitForm($selector, $inputs, $clear = false)
    {
        $form = $this->byCssSelector($selector);
        $this->typeInformation($inputs, $clear);
        $form->submit();
        return $this;
    }
    /**
      * Will attempt to find an element by different patterns
      * If xpath is provided, will attempt to find by that first.
      *
      * @param $value
      * @param null $xpath
      *
      * @throws Exception
      *
      * @return \PHPUnit_Extensions_Selenium2TestCase_Element
      */
    protected function findElement($value, $xpath = null)
    {
        try {
            if (!is_null($xpath)) {
                return $this->byXPath($xpath);
            }
        } catch (\Exception $e) {
        }
        try {
            return $this->byId($value);
        } catch (\Exception $e) {
        }
        try {
            return $this->byName($value);
        } catch (\Exception $e) {
        }
        try {
            return $this->byCssSelector($value);
        } catch (\Exception $e) {
        }
        throw new \Exception('Cannot find element: '.$value.' isn\'t visible on the page');
    }

    /**
     * Wrapper for findElement which finds element and clicks it
     * @param $value
     * @param null $xpath
     * @return $this
     */
    protected function findAndClickElement($value, $xpath = null)
    {
        $this->findElement($value, $xpath)->click();
        return $this;
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
    protected function waitForElementPresent($target, $timeout = 60)
    {
        for ($second = 0; ; $second++) {
            if ($second >= $timeout) {
                $this->fail('timeout');
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
    protected function waitForElementNotPresent($target, $timeout = 60)
    {
        for ($second = 0; ; $second++) {
            if ($second >= $timeout) {
                $this->fail('timeout');
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

    protected $waitForTypes = ['Id', 'CssSelector', 'ClassName', 'XPath'];
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
    protected function waitForElement($type, $value, $timeout)
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
    protected function waitForElementsWithClass($class, $timeout = 2000)
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
    protected function waitForElementWithId($id, $timeout = 2000)
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
    protected function waitForElementWithXPath($xpath, $timeout = 2000)
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
     * CLicks a link using text inside it
     * @param  string $value Text in link
     * @return $this
     */
    protected function clickLink($value='')
    {
        try {
            $this->byPartialLinkText($value)->click();
        } catch (\Exception $e) {
            throw new \Exception("Can't find a link with $value text.");
        }
        return $this;
    }

    /**
     * Click the label with the value in it
     * @param  $value substring in label to click
     * @return $this
     */
    protected function clickLabel($value)
    {
        $this->findElement($value, "//label[contains(., '{$value}')]")
             ->click();
        return $this;
    }
}
