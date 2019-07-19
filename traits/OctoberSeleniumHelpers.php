<?php namespace Initbiz\Selenium2tests\Traits;

use PHPUnit_Extensions_Selenium2TestCase_Keys as Keys;

/**
 * Trait with Selenium 2 helper methods for October
 */

trait OctoberSeleniumHelpers
{
    // Methods to log in and out from backend

    /**
     * Method used to go to OctoberCMS's backend. If not signed in, it will automatically try to sign in
     * @return $this
     */
    public function gotoBackend($url = "backend", $username = '', $password = '')
    {
        $url = $this->backendUri . '/' . $url;

        $this->visit($url);

        if (strpos($this->url(), 'auth/signin')) {
            $this->signInToBackend($username, $password);
            $this->visit($url);
        }

        return $this;
    }

    /**
     * Sign in to backend
     * @param  string $username overrided username
     * @param  string $password overrided password
     * @return $this
     */
    public function signInToBackend($username = '', $password = '')
    {
        if (!strpos($this->url(), 'auth/signin')) {
            $this->visit($this->backendUri.'/backend/auth/signin');
        }

        if ($username === '') {
            $username = $this->backendAdmin['username'];
        }

        if ($password === '') {
            $password = $this->backendAdmin['password'];
        }

        $this->type('login', $username)
             ->type('password', $password)
             ->findElement("Login button", "//button[@type='submit']")
             ->click();

        // Waiting for element that is always going to be displayed in backend after successful logging in
        $this->waitFor('.mainmenu-toolbar');

        return $this;
    }

    /**
     * Sign out from backend
     * @return $this
     */
    public function signOutFromBackend()
    {
        $url = $this->backendUri . '/backend/auth/signout';

        $this->visit($url);

        return $this;
    }

    // Moving around backend

    /**
     * Method to go to page by clicking the backend navigation
     * @param  string $mainNavEntry Main navigation label
     * @param  string $subNavEntry  Sub navigation label
     * @param  bool $forceClick if set to true, click even if active
     * @return void
     */
    public function clickNav(string $mainNavLabel, string $sideNavLabel = '', bool $forceClick = false)
    {
        $this->clickNavLink($mainNavLabel, $forceClick);

        if ($sideNavLabel !== '') {
            if ($mainNavLabel === 'Settings') {
                $this->clickSettingsNavLink($sideNavLabel, $forceClick);
            } else {
                $this->clickSideNavLink($sideNavLabel, $forceClick);
            }
        }
    }

    /**
     * Clicks backend navigation link only if it is not active
     * @param  string $label Label of nav to be clicked
     * @param  bool $forceClick if set to true, click even if active
     * @return $this
     */
    public function clickNavLink(string $label, bool $forceClick = false)
    {
        // Do not click the element if it's already active
        $elementActive = true;
        try {
            $this->findElement('Nav link', "//nav[@id='layout-mainmenu']//li[contains(concat(' ',normalize-space(@class),' '),' active ') and contains(., '" . $label . "')]");
        } catch (\Exception $e) {
            $elementActive = false;
        }

        if (!$elementActive || $forceClick) {
            $this->findAndClickElement('Nav link', "//nav[@id='layout-mainmenu']//li[contains(., '" . $label . "')]");
        }

        return $this;
    }

    /**
     * Clicks backend side navigation link only if it is not active
     * @param  string $label Label of side nav element to be clicked
     * @param  bool $forceClick if set to true, click even if active
     * @return $this
     */
    public function clickSideNavLink(string $label, bool $forceClick = false)
    {
        // Do not click the element if it's already active
        $elementActive = true;
        try {
            $this->findElement('Sidenav link', "//nav[@id='layout-sidenav']//li[contains(concat(' ',normalize-space(@class),' '),' active ') and contains(., '" . $label . "')]");
        } catch (\Exception $e) {
            $elementActive = false;
        }

        if (!$elementActive || $forceClick) {
            $this->findAndClickElement('Sidenav link', "//nav[@id='layout-sidenav']//li[contains(., '" . $label . "')]");
        }

        return $this;
    }

    /**
     * Clicks backend settings navigation link only if it is not active
     * @param  string $label Label of settings nav element to be clicked
     * @param  bool $forceClick if set to true, click even if active
     * @return $this
     */
    public function clickSettingsNavLink(string $label, bool $forceClick = false)
    {
        // Do not click the element if it's already active
        $elementActive = true;
        try {
            $this->findElement('Settings nav link', "//ul[@class='top-level']//li[contains(concat(' ',normalize-space(@class),' '),' active ') and contains(., '" . $label . "')]");
        } catch (\Exception $e) {
            $elementActive = false;
        }

        if (!$elementActive || $forceClick) {
            $this->findAndClickElement('Settings nav link', "//ul[@class='top-level']//span[@class='header' and contains(., '" . $label . "')]");
        }

        return $this;
    }

    // Backend lists

    /**
     * Get record ID from backend list using search form
     * @param   $uniqueValue unique value for searching in searchform
     * @param   $pageUrl backend page URL where the list resides
     * @return  $id record ID
     */
    public function getRecordID($uniqueValue, $pageUrl = '')
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
    public function clickRowInBackendList($uniqueValue)
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
    public function checkRowIdInBackend($id)
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
    public function typeInBackendSearch($value='', $clear=false)
    {
        $this->type('listToolbarSearch[term]', $value, $clear);
        return $this;
    }

    // Backend forms

    /**
    * "Select" a select2 field.
    *
    * @param $element
    * @param $value
    */
    public function select2($element, $value)
    {
        //Sometimes select2 can't understand the click, try to click it again then
        for ($i=0; $i < 5; $i++) {
            try {
                $this->findAndClickElement($element)
                     ->waitFor('select2-search__field', 1);
                break;
            } catch (\Exception $e) {
            }
        }
        $element = $this->findElement('input.select2-search__field');
        $element->value($value.Keys::ENTER);

        return $this;
    }

    public function checkSwitchOn($switchId)
    {
        $value = $this->findElement($switchId)->attribute('checked');
        if ($value !== "true") {
            $this->toggleSwitch($switchId);
        }
        return $this;
    }

    public function checkSwitchOff($switchId)
    {
        $value = $this->findElement($switchId)->attribute('checked');
        if ($value === "true") {
            $this->toggleSwitch($switchId);
        }
        return $this;
    }

    public function toggleSwitch($switchId)
    {
        $this->findAndClickElement($switchId);
        return $this;
    }


    public function selectRecordfinder($parentElementId, $value)
    {
        $recordFinderButton = $this->findElement('Record finder button', "//div[@id='". $parentElementId ."']//button");
        $recordFinderButton->click();
        $this->waitFor('close', 2000);

        $searchBox = $this->findElement('.recordfinder-search');
        $searchBox->clear();
        $searchBox->value($value);
        // Wait for AJAX to complete filtering, to change to waitFor or something
        $this->hold(2);

        $this->findElement("Recordfinder row with: " . $value, '//*[@class="recordfinder-list list-flush"]//table/tbody/tr[1]/td[2]')
             ->click();

        $this->waitUntilMissing('.close');
    }

    // Helpers

    /**
    * Method that waits for a flash message to appear
     * @param   $class class of element for waiting
    * @return   $this
    */
    public function waitForFlashMessage($class = 'flash-message', $timeout = 10000)
    {
        return $this->waitFor($class, $timeout);
    }

    /**
    * Method that asserts that Flash message is visible
    * @return $this
    */
    public function seeFlash()
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
}
