<?php namespace Initbiz\Selenium2tests\Classes;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\ElementResolver as DuskElementResolver;

class ElementResolver extends DuskElementResolver
{
    public function find($selector, $xpath = null)
    {
        try {
            return $this->findOrFail($selector, $xpath);
        } catch (\Exception $e) {
            //
        }
    }

    public function findOrFail($selector, $xpath = null)
    {
        try {
            if (!is_null($xpath)) {
                return $this->driver->findElement(WebDriverBy::xpath($xpath));
            }
        } catch (\Exception $e) {
        }

        if (empty($selector)) {
            $selector = $this->format($selector);
        }

        try {
            return $this->driver->findElement(WebDriverBy::id($selector));
        } catch (\Exception $e) {
        }

        try {
            return $this->driver->findElement(WebDriverBy::name($selector));
        } catch (\Exception $e) {
        }

        try {
            return $this->driver->findElement(WebDriverBy::cssSelector($selector));
        } catch (\Exception $e) {
        }

        throw new \Exception('Cannot find element: '.$selector.' isn\'t visible on the page');
    }
}
