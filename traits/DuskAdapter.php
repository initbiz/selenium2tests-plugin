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
}
