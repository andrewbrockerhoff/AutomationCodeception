<?php
namespace Page;

use Exception;


class ItemsPage
{
    // should be passed in wrapped methods to fix practitest fails
    const PRACTITEST_FIX = "practitest_fix";
    
    public static $cigarSearchURL = '/cigar-search?displayMobile=false';
    public static $checkoutURL = '/checkout?displayMobile=false';
    public static $title = '//h1';
    public static $countryDropDownList = '//*[@class="oswald half"]';
    // Items Block
    public static $firstItem = '//*[@ class="dealscroller"]//a[1]';
    public static $quickViewButton = '//*[@class="dealscroller"]/a[1]/div[1]/div[1]';
    public static $itemWindow = '//*[@class="subtitle oswald"]';
    public static $addToCartButton = '//*[@class="half left cgray"]//a[text()="add to cart"]';
    public static $notificationAddedToCart = '//span[contains(text(),"Item added to cart:")]';
    public static $proceedToCartButton = '//span[contains(text(),"proceed to cart")]';
    public static $modalWindow = "//*[@class='modalbox']";
    public static $modalAddToCartButton = "//*[contains(@href, 'added-to-cart')]";
    // proceed to checkout
    public static $cartTitle = '//*[@class="cart_title"]';
    public static $proceedToCheckoutButton = '//*[@class="checkoutbuttons"]/a[1]';
    // Login Checkout Page
    public static $loginFormLocator = '//*[@id="loginform"]/div[1]/p';

    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function goToCigarSearchPage()
    {
        $I = $this->tester;
        $I->amOnPage(self::$cigarSearchURL);
        $I->canSee('Cigar Search', self::$title);
    }

    public function addRandomItem()
    {
        $I = $this->tester;

        $x = rand(2, 170);
        $I->amOnPage('/cigar-search?displayMobile=false&results_per_page=60&page_number=' . $x); //page $x
        $I->canSee('Cigar Search', self::$title);
        $k = rand(1, 60);
        sleep(1);
        $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']']); //item $k
        $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
        $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']/div[1]/div[1]']);
        try {
            $I->waitForElementWrap(self::$addToCartButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$addToCartButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->amOnPage('/cigar-search?displayMobile=false&results_per_page=1&page_number=1');
            $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[1]']);
            $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitForElement(self::$addToCartButton);
            $I->waitAndClick(self::$addToCartButton);
        }
        try {
            $I->waitForElementWrap(self::$modalWindow, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$modalAddToCartButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->waitForElement(self::$proceedToCheckoutButton);
            $I->waitAndClick(self::$proceedToCheckoutButton);
        }
    }

    public function addCigarillosItem()
    {
        $I = $this->tester;
        $I->amOnPage('/cigars/cigarillos-cigars?displayMobile=false&sort=best_desc&shape=Cigarillo&results_per_page=60&page_number=' . rand(1, 5));

        $k = rand(1, 60);
        sleep(1);
        $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']']); //item $k
        $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
        $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']/div[1]/div[1]']);
        if (count($I->grabMultiple(
                "//a[contains(., 'Backorder') or contains(., 'Item not available') or contains(., 'Preorder')]"
            )) > 0) {
            echo "\nUnavailable item (Cigarillos). Skipping.\n";
            $I->amOnPage(self::$checkoutURL);
            return;
        }

        try {
            $I->waitForElementWrap(self::$addToCartButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$addToCartButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->amOnPage('/cigars/cigarillos-cigars?displayMobile=false&sort=best_desc&shape=Cigarillo&results_per_page=1&page_number=1');
            $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[1]']);
            $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitForElement(self::$addToCartButton);
            $I->waitAndClick(self::$addToCartButton);
        }
        try {
            $I->waitForElementWrap(self::$proceedToCheckoutButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$proceedToCheckoutButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->waitAndClick('//*[@class="tac"]/div[2]/a[1]');
            try {
                $I->waitForElement(self::$proceedToCheckoutButton);
                $I->waitAndClick(self::$proceedToCheckoutButton);
            } catch (Exception $e) {
                $I->see("Item not available");
                echo "\nUnavailable item (Cigarillos). Skipping.\n";
                $I->amOnPage(self::$checkoutURL);
            }
        }

    }

    public function addButaneItem()
    {
        $I = $this->tester;
        $I->amOnPage('/search?kw=butane&displayMobile=false');

        $k = 1;
        $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']']); //item $k
        $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
        $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']/div[1]/div[1]']);
        if (count($I->grabMultiple(
                "//a[contains(., 'Backorder') or contains(., 'Item not available') or contains(., 'Preorder')]"
            )) > 0) {
            echo "\nUnavailable item (Butane). Skipping.\n";
            $I->amOnPage(self::$checkoutURL);
            return;
        }
        try {
            $I->waitForElementWrap(self::$addToCartButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$addToCartButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->amOnPage('/search?kw=butane&displayMobile=false');
            $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[1]']);
            $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitForElement(self::$addToCartButton);
            $I->waitAndClick(self::$addToCartButton);
        }
        try {
            $I->waitForElementWrap(self::$proceedToCheckoutButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$proceedToCheckoutButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->see("Item not available");
            echo "\nUnavailable item (Butane). Skipping.\n";
            $I->amOnPage(self::$checkoutURL);
        }
    }

    public function addLargeHumidorsItem()
    {
        $I = $this->tester;
        $I->amOnPage('/search?kw=large%20humidors&displayMobile=false');

        $k = 2;
        $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']']); //item $k
        $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
        $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[' . $k . ']/div[1]/div[1]']);
        if (count($I->grabMultiple(
                "//a[contains(., 'Backorder') or contains(., 'Item not available') or contains(., 'Preorder')]"
            )) > 0) {
            echo "\nUnavailable item (Large Humidors). Skipping.\n";
            $I->amOnPage(self::$checkoutURL);
            return;
        }

        try {
            $I->waitForElementWrap(self::$addToCartButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$addToCartButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->amOnPage('/search?kw=large%20humidors&displayMobile=false');
            $I->moveMouseOver(['xpath' => '//*[@class="dealscroller"]/a[1]']);
            $I->waitForElement(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitAndClick(['xpath' => '//*[@class="dealscroller"]/a[1]/div[1]/div[1]']);
            $I->waitForElement(self::$addToCartButton);
            $I->waitAndClick(self::$addToCartButton);
        }
        try {
            $I->waitForElementWrap(self::$proceedToCheckoutButton, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$proceedToCheckoutButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->see("Item not available");
            echo "\nUnavailable item (Large Humidors). Skipping.\n";
            $I->amOnPage(self::$checkoutURL);
        }
    }

    public function addBellaVanillaCigarillo()
    {
        $I = $this->tester;
        $I->amOnPage('/add-to-cart?ihdnum=17120&itemtype=B&is_bg=N');
        try {
            $I->waitForElementWrap(self::$modalWindow, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$modalAddToCartButton, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->waitForElement(self::$proceedToCheckoutButton);
            $I->waitAndClick(self::$proceedToCheckoutButton);
        }
    }
}
