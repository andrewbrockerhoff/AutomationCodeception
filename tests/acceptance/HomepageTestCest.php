<?php

use \Step\Acceptance;

class HomepageTestCest
{
    /**
     * Run after every scenario
     */
    public function _after(\AcceptanceTester $I)
    {
        $I->sendResultToPractiTest();
    }

    public function _failed(AcceptanceTester $I)
    {
        $I->sendResultToPractiTest();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testMainBanner(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkMainBanners();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testFourBanners(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkFourBanners();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testPopularCigarBrands(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkPopularCigarBrands();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testCustomerServiceSection(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkCustomerServicePic();
        $homePage->checkWelcomeText();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testFooterMenu(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkFooterMenuLinks();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testFooterLinks(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkFooterSocialLinks();
        $homePage->checkFooterBannersLinks();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testMainMenu(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkMainMenu();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testCartButton(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkCartButton();
    }

    /**
     * @param \Page\HomePage $homePage
     * @group homepage
     */
    public function testBrandsByLetters(\Page\HomePage $homePage)
    {
        $homePage->goToHomePage();
        $homePage->checkBrandsByLetters();
    }
}