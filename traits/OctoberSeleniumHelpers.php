<?php namespace Initbiz\Selenium2tests\Traits;

/**
 * Trait with Selenium 2 helper methods for October
 */

trait OctoberSeleniumHelpers
{
    /**
     * Method used to sign in to OctoberCMS backend using params from selenium.php
     * @return $this
     */
    protected function signInToBackend()
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL)
             ->type(TEST_SELENIUM_USER, 'login')
             ->type(TEST_SELENIUM_PASS, 'password')
             ->findElement("Login button", "//button[@type='submit']")
             ->click();
        return $this;
    }

    /**
     * Sign out from backend
     * @return $this
     */
    protected function signOutFromBackend()
    {
        $this->visit(TEST_SELENIUM_BACKEND_URL.'backend/auth/signout');
        return $this;
    }

    /**
    * Method that waits for a flash message to appear
     * @param   $class class of element for waiting
    * @return   $this
    */
    protected function waitForFlashMessage($class = 'flash-message', $timeout = 2000)
    {
        return $this->waitForElementsWithClass($class, $timeout);
    }

    /**
    * Method that asserts that Flash message is visible
    * @return $this
    */
    protected function seeFlash()
    {
        try {
            $this->waitForFlashMessage();
        } catch (Exception $e) {
            throw new \Exception('Waiting for flash timed out');
        }

        try {
            $this->assertTrue(is_a($this->byCssSelector('p.flash-message'), 'PHPUnit_Extensions_Selenium2TestCase_Element'));
        } catch (Exception $e) {
            throw new \Exception('Flash is not visible');
        }

        return $this;
    }

    /**
     * Get record ID from backend list using search form
     * @param   $uniqueValue unique value for searching in searchform
     * @param   $pageUrl backend page URL where the list resides
     * @return  $id record ID
     */
    protected function getRecordID($uniqueValue, $pageUrl = '')
    {
        if (!empty($pageUrl)) {
            $this->visit($pageUrl);
        }
        $this->typeInBackendSearch($uniqueValue, true);
        $this->hold(2); //TODO wait for ajax to reload list
        $link = $this->findElement("Link for: ".$uniqueValue, '//*[@id="Lists"]/div/table/tbody/tr[1]/td[2]/a')
                     ->attribute('href');
        $linkParams = explode("/", $link);
        //TODO: try catch
        $id = end($linkParams);
        return $id?(int)$id:false;
    }

    /**
     * click row in backend list containing the $uniqueValue
     * @param  $uniqueValue what to search in list
     * @return $this
     */
    protected function clickRowInBackendList($uniqueValue)
    {
        $this->typeInBackendSearch($uniqueValue, true);
        $this->hold(2); //TODO wait for ajax to reload list
        $this->findElement("Row with: ".$uniqueValue, '//*[@id="Lists"]/div/table/tbody/tr[1]/td[2]')
             ->click();
        $this->hold(2);
        return $this;
    }

    /**
     * Check the checkbox of row with $id in backend list
     * @param $id id of record
     * @return $this
     */
    protected function checkRowIdInBackend($id)
    {
        $this->findElement($id, "//label[@for='Lists-checkbox-{$id}']")
             ->click();
        return $this;
    }

    /**
     * Type words into backend seach form
     * @param  string $value string to type in search form
     * @param  boolean $clear clear the searchbox or not
     * @return $this
     */
    protected function typeInBackendSearch($value='', $clear=false)
    {
        $this->type($value, 'listToolbarSearch[term]', $clear);
        return $this;
    }
}
