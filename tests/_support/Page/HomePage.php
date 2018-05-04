<?php
namespace Page;

use Exception;


class HomePage
{
    public static $homePageURL =  '/';
    public static $cigarBrandListURL = "/cigar-brand-list";
    public static $homeBannerSection = '//*[@class="headbanner"]';
    public static $bannerPagination = '//*[@class="jsslidenums"]';
    public static $tallMerchandisingBanner = '//a[@title="Herrera"]';
    public static $homePageLogo = '//*[@class="logo"]';
    public static $mainBanners = "//*[contains(@class, 'jsgallery') and contains(@class, 'gallready')]/a";
    public static $fourBanners = '//*[@class="tbanner"]';
    public static $popularCigarBrands = '//*[@class="flexrow"]/a';
    public static $customerServicePic = '//*[contains(@class, "customer-service")]';
    public static $welcomeText = '//*[@class="hometext"]';
    public static $footerMenu = '//*[@class="cannonflex"]';
    public static $footerSocialLinks = '//*[@class="footersocial"]/a';
    public static $footerBannersLinks = '//*[@class="footerlogos"]/div/div/div/a';
    public static $mainMenu = "//*[contains(@class, 'mainmenu')]";
    public static $cartButton = "//*[contains(@class, 'cart') and contains(@class, 'left')]";
    public static $brandsByLetter = "//*[@class='letterlist']/ul";
    public static $notFound404Text = "404: This is not the page you are looking for.";
    public static $notFound500Text = "500 - Internal server error.";

    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function goToHomePage()
    {
        $I = $this->tester;
        $I->amOnPage(self::$homePageURL);
        $I->waitForElementVisible(self::$homePageLogo);
    }

    public function checkOnPromo()
    {
        $I = $this->tester;
        $I->seeInCurrentUrl("/promo");
    }

    public function checkMainBanners()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$mainBanners);
        $urls = $I->grabMultiple(self::$mainBanners, "href");
        foreach ($urls as $url) {
            $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
            $I->assertNotEmpty($res);
        }
    }

    public function checkFourBanners()
    {
        $I = $this->tester;
        $I->canSeeNumberOfElements(self::$fourBanners, 4);
        $urls = $I->grabMultiple(self::$fourBanners, "href");
        foreach ($urls as $url) {
            $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
            $I->assertNotEmpty($res);
        }
    }

    public function checkPopularCigarBrands()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$popularCigarBrands);
        $urls = $I->grabMultiple(self::$popularCigarBrands, "href");
        foreach ($urls as $url) {
            $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
            $I->assertNotEmpty($res);
        }
    }

    public function checkCustomerServicePic()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$customerServicePic);
    }

    public function checkWelcomeText()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$welcomeText);
    }

    public function checkFooterMenuLinks()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$footerMenu);
        $urls = $I->grabMultiple(self::$footerMenu, "href");
        foreach ($urls as $url) {
            $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
            $I->assertNotEmpty($res);
        }
    }

    public function checkFooterSocialLinks()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$footerSocialLinks);
        $urls = $I->grabMultiple(self::$footerSocialLinks, "href");
        foreach ($urls as $url) {
            $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
            $I->assertNotEmpty($res);
        }
    }

    public function checkFooterBannersLinks()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$footerBannersLinks);
        $urls = $I->grabMultiple(self::$footerBannersLinks, "href");
        foreach ($urls as $url) {
            $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
            $I->assertNotEmpty($res);
        }
    }

    public function checkCartButton()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$cartButton);
        $url = $I->grabAttributeFrom(self::$cartButton, "href");
        $res = \SimpleRestClient::get($url, $params = [], $headers = [], $check_status = true, $decode_response = false);
        $I->assertNotEmpty($res);
    }

    public function checkMainMenu()
    {
        $I = $this->tester;
        $I->canSeeElement(self::$mainMenu);
        $I->waitAndClick(self::$mainMenu);
        $urls = $I->grabMultiple(self::$mainMenu . "/ul/li/a", "href");
        $li_num = 1;
        foreach ($urls as $k => $url) {
            if (strstr($url, "http") === false) continue;
            $submenu_items = $I->grabMultiple(self::$mainMenu . "/ul/li[" . $li_num . "]/ul/li/a", "innerText");
            $submenu_urls = $I->grabMultiple(self::$mainMenu . "/ul/li[" . $li_num . "]/ul/li/a", "href");
            foreach ($submenu_urls as $k => $url) {
                if (strstr($url, "http") === false) continue;
                $I->amOnUrl($url);
                $I->waitForElement("//h1");
                $I->dontSee(self::$notFound404Text, "h1");
                $I->dontSeeInTitle(self::$notFound500Text);
            }
            $li_num++;
        }
    }
    public function checkBrandsByLetters()
    {
        $I = $this->tester;
        $I->amOnPage(self::$cigarBrandListURL);
        $brands = $I->grabMultiple(self::$brandsByLetter);
        foreach ($brands as $brand) {
            $brand_list = explode("\n", $brand);
            $I->assertGreaterThanOrEqual(1, count($brand_list));
        }
    }
}