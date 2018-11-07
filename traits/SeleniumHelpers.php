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
    public function check($name)
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
    public function visit($path)
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
    public function scroll($amount)
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
    public function type($value, $name, $clear = false)
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
    public function press($text)
    {
        try {
            $this->findElement($text, "//button[contains(., '{$text}')]")->click();
            return $this;
        } catch (\Exception $e) {
        }
        try {
            $this->findElement($text, "//button[@title='{$text}']")->click();
            return $this;
        } catch (\Exception $e) {
        }
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
    public function see($text, $tag = 'body')
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
    public function notSee($text, $tag = 'body')
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
    public function select($element, $value)
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
    public function seePageIs($path)
    {
        $this->assertEquals($this->baseUrl . $path, $this->url());
        return $this;
    }

    /**
     * Hold for $seconds seconds
     * @param  int $seconds number of seconds to hold
     * @return $this
     */
    public function hold($seconds)
    {
        sleep($seconds);
        return $this;
    }

    /**
     * Hold for $milliSeconds milliseconds
     * @param  int $milliseconds number of milliseconds to hold
     * @return $this
     */
    public function mhold($milliSeconds)
    {
        $microSeconds = $milliSeconds * 1000;
        usleep($microSeconds);
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
    public function typeInformation($information, $clear = false)
    {
        foreach ($information as $element => $item) {
            $this->type($item, $element, $clear);
        }
        return $this;
    }
    public function submitForm($selector, $inputs, $clear = false)
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
    public function findElement($value, $xpath = null)
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
      * Will attempt to find an elements by different patterns
      * If xpath is provided, will attempt to find by that first.
      *
      * @param $value
      * @param null $xpath
      *
      * @return \PHPUnit_Extensions_Selenium2TestCase_Element[]|NULL
      */
    public function findElements($value, $xpath = null)
    {
        try {
            if (!is_null($xpath)) {
                return $this->elements($this->using('xpath')->value($xpath));
            }
        } catch (\Exception $e) {
        }
        try {
            return $this->elements($this->using('id')->value($vaule));
        } catch (\Exception $e) {
        }
        try {
            return $this->elements($this->using('xpath')->value('.//*[@name="'.$value.'"]'));
        } catch (\Exception $e) {
        }
        try {
            return $this->elements($this->using('css selector')->value($value));
        } catch (\Exception $e) {
        }
        return null;
    }
    /**
     * Wrapper for findElement which finds element and clicks it
     * @param $value
     * @param null $xpath
     * @return $this
     */
    public function findAndClickElement($value, $xpath = null)
    {
        $this->findElement($value, $xpath)->click();
        return $this;
    }

    public function isElementPresent($target)
    {
        try {
            $this->findElement($target);
        } catch (\Exception $e) {
            return false;
        }

        $displayed = $this->findElement($target)->displayed();

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
     * CLicks a link using text inside it
     * @param  string $value Text in link
     * @return $this
     */
    public function clickLink($value='')
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
    public function clickLabel($value)
    {
        $this->findElement($value, "//label[contains(., '{$value}')]")
             ->click();
        return $this;
    }

    /**
      * Will attempt to scroll to an element by different patterns.
      * If xpath is provided, will attempt to scroll by that first.
      * If value is name or class name, will attempt to scroll to first occurrence of the element.
      *
      * @param $value
      * @param null $xpath
      *
      * @throws Exception
      *
      * @return \PHPUnit_Extensions_Selenium2TestCase_Element
      */
    public function scrollToElement($value, $xpath = null)
    {
        try {
            if (!is_null($xpath)) {
                $this->execute(array(
                    'script' => 'document.evaluate(' . $xpath . ', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.ScrollIntoView(true);',
                    'args' => array(),
                ));
                return $this;
            }
        } catch (\Exception $e) {
        }
        try {
            $this->execute(array(
                'script' => 'document.getElementById("' . $value . '").scrollIntoView(true);',
                'args' => array(),
            ));
            return $this;
        } catch (\Exception $e) {
        }
        try {
            $this->execute(array(
                'script' => 'elements=document.getElementsByName("' . $value . '");elements[0].scrollIntoView(true);',
                'args' => array(),
            ));
            return $this;
        } catch (\Exception $e) {
        }
        try {
            $this->execute(array(
                'script' => 'elements=document.getElementsByClassName("' . $value . '");elements[0].scrollIntoView(true);',
                'args' => array(),
            ));
            return $this;
        } catch (\Exception $e) {
        }
        throw new \Exception('Cannot find element: '.$value.' isn\'t visible on the page');
    }
}
