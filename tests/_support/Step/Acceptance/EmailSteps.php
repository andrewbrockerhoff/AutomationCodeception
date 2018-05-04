<?php
namespace Step\Acceptance;


use Exception;

class EmailSteps extends \AcceptanceTester
{

    public function loginEmailYahoo()
    {
        $I = $this;
        $I->amOnUrl("https://mg.mail.yahoo.com/neo/m/launch");
        $I->fillField('//*[@id="login-username"]', 'famoussmokeshoptester@yahoo.com');
        $I->click('//*[@id="login-signin"]');
        $I->wait(3);
        $I->waitForElementVisible('//*[@id="login-passwd"]');
        $I->fillField('//*[@id="login-passwd"]', '!1qwerty');
        $I->click('//*[@id="login-signin"]');
        $I->wait(4);
        $I->click('//*[@id="login-signin"]');
        $I->wait(4);
        $I->fillField('//*[@id="login-passwd"]', '!1qwerty');
        $I->click('//*[@id="login-signin"]');
        $I->wait(5);
        $I->waitForElement('//*[@id="datatable"]/tbody/tr[1]/td[3]/a/table/tbody/tr[2]/td/div');
        $I->click('//*[@id="datatable"]/tbody/tr[1]/td[3]/a/table/tbody/tr[2]/td/div');
      //  $I->waitForElement('//*[@class="list-view-items-page"]/div//*[contains(text(),"famous")]');
      //  $I->click('//*[@class="list-view-items-page"]/div//span[contains(text(),"famous")]');
        $I->waitForText('password has been reset.');
       // $I->waitAndClick('//*[@alt="RESET MY PASSWORD >"]');
        $I->waitAndClick('//*[@alt="RESET MY PASSWORD >"]');
        $I->wait(2);
        $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
            $handles = $webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->waitForElement('.//*[@id="pwd"]');


        
    }


// //*[@id='datatable']/tbody/tr[1]/td[3]/a
/*
 *  $I->waitForElement('//*[@class="icon-delete"]');
        $I->click('//*[@class="icon-delete"]');
        try {
            $I->waitForText('Your Inbox folder is empty');
        } catch (Exception $e){}

        $I->click('//*[@id="match-messagelist-sizing"]//label');
        $I->wait(1);
        $I->waitForElement('//*[@class="icon-delete"]');
        $I->click('//*[@class="icon-delete"]');
        try {$I->waitForElement('//div[@id="modalOverlay"]//button');
            $I->click('//div[@id="modalOverlay"]//button');} catch (Exception $e){}
        $I->waitForText('Your Inbox folder is empty');
 */

}