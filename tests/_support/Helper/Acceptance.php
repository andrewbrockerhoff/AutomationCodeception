<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    const
        WEBDRIVER = 'WebDriver',
        CUSTOM_WEBDRIVER = 'Helper\CustomWebDriver';

    public function scrollDown($scroll_Y = null)
    {
        $driv = $this->getModule(self::CUSTOM_WEBDRIVER)->webDriver;
        $scroll_Y = abs(intval($scroll_Y));
        $script= '';
        if (!$scroll_Y) {
            $script = '$heig = window.screen.availHeight; ';
        } else {
            $script = '$heig = ' . $scroll_Y . '; ';}
        $script .= 'window.scrollBy(0, $heig)';
        $driv->executeScript($script);
    }

    public function scrollUp($scroll_Y = null)
    {
        $driv = $this->getModule(self::CUSTOM_WEBDRIVER)->webDriver;
        $scroll_Y = -abs(intval($scroll_Y));
        $script= '';
        if (!$scroll_Y) {
            $script = '$heig = -window.screen.availHeight; ';
        } else {
            $script = '$heig = ' . $scroll_Y . '; ';}
        $script .= 'window.scrollBy(0, $heig)';
        $driv->executeScript($script);
    }

    public function waitAlertAndAccept($timeout = 5, $interval = 200)
    {
        $driv = $this->getModule(self::CUSTOM_WEBDRIVER)->webDriver;
        $alert = $driv->wait($timeout, $interval)->until(function($driv) {
            try {
                $alert = $driv->switchTo()->alert();
                $alert->getText();
                return $alert;
            } catch (NoAlertOpenException $e) {
                return null;
            }
        });
        $alert->accept();
    }

    public function waitForAjax($timeout = 15, $interval = 200)
    {
        $driv = $this->getModule(self::CUSTOM_WEBDRIVER)->webDriver;
        $driv->wait($timeout, $interval)->until(function($driv) {
            $condition = 'return (jQuery.active == 0);';
            return $driv->executeScript($condition);
        });
    }

    public function secondWindow()
    {
        $I = $this;
        $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
            $handles = $webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
    }

   function localStorageClear($key = null)
    {
        $wb = $this->getModule(self::CUSTOM_WEBDRIVER);
        $wb->executeJS('window.localStorage.' . ((is_string($key) == true) ? "removeItem(\"$key\"" : 'clear(') . ');');
    }

     **/
    public function seeImage($element, $minSizeXY = 10)
    {
        $wb = $this->getModule(self::CUSTOM_WEBDRIVER);
        $realSizeX = $wb->grabAttributeFrom($element, 'naturalWidth');
        $realSizeY = $wb->grabAttributeFrom($element, 'naturalHeight');
        $this->assertGreaterThanOrEqual($minSizeXY, $realSizeX);
        $this->assertGreaterThanOrEqual($minSizeXY, $realSizeY);
    }

    public function waitAndClick($element)
    {
        $wb = $this->getModule(self::CUSTOM_WEBDRIVER);
        $wb->waitForElement($element);
        $wb->click($element);
    }

    public function seeLink($text, $url = null)
    {
        return $this->getScenario()->runStep(new \Codeception\Step\Assertion('seeLink', func_get_args()));
    }

    public function seeInTitle($title)
    {
        return $this->getScenario()->runStep(new \Codeception\Step\Assertion('seeInTitle', func_get_args()));
    }
}
