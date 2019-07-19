<?php namespace Initbiz\Selenium2tests\Traits;

/**
 * Trait with Selenium 2 adapter methods
 * It's created to make test written for old Selenium 2 sytax work with the Dusk syntax
 * Meant to be used in Browser class
 */

trait DuskAdapter
{
    public function findAndClickElement($value, $xpath = null) {
        $this->findElement($value, $xpath)->click();
        return $this;
    }

    public function findElement($value, $xpath = null)
    {
        return $this->resolver->find($value, $xpath);
    }

    public function url()
    {
        return $this->driver->getCurrentURL();
    }

    public function scroll($pixels)
    {
        $this->driver->executeScript('window.scrollBy(0, ' . $pixels .');'); 
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
                $this->driver->executeScript('document.evaluate(' . $xpath . ', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.ScrollIntoView(true);'); 
                return $this;
            }
        } catch (\Exception $e) {
        }
        try {
            $this->driver->executeScript('document.getElementById("' . $value . '").scrollIntoView(true);'); 
            return $this;
        } catch (\Exception $e) {
        }
        try {
            $this->driver->executeScript('elements=document.getElementsByName("' . $value . '");elements[0].scrollIntoView(true);'); 
            return $this;
        } catch (\Exception $e) {
        }
        try {
            $this->driver->executeScript('elements=document.getElementsByClassName("' . $value . '");elements[0].scrollIntoView(true);'); 
            return $this;
        } catch (\Exception $e) {
        }
        throw new \Exception('Cannot find element: '.$value.' isn\'t visible on the page');
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
}
