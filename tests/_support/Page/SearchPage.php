<?php

namespace Page;


class SearchPage
{
    const DEFAULT_RESULTS_ON_PAGE = 60;

    public static $searchPageUrl = "/search";
    public static $sortByBestMatch = ["sort" => "best_desc"];
    public static $filterByPrice = "price_preset_range";
    public static $filterPromoOnSale = "is_on_sale";
    public static $filterByBrand = "brand_code";
    public static $filterByRating = "avg_rating_preset_range";

    public static $searchField = "//*[@class='searchinput']";
    public static $itemsPrice = "//*[@class='cblack']";
    public static $filters = "//*[@class='filterlink']";
    public static $onSale = "//*[@class='cred']";
    public static $brandName = "//*[contains(@class, 'dealitembrand')]";
    public static $rating = "//*[contains(@class, 'dealitemdesc') and contains(@class, 'cltgray')]";
    public static $searchButton = "//*[@class='searchboxbtn']";

    public static $brandMap = [
        "ACID" => "ACI"
    ];

    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function goToSearchPage()
    {
        $I = $this->tester;
        $I->amOnPage(self::$searchPageUrl);
    }

    public function checkFilterByPrice($min, $max)
    {
        $I = $this->tester;
        list($urls, $values) = $this->getFilters();
        $url = "";
        foreach ($urls as $k => $v) {
            if (strstr($v, self::$filterByPrice) === false) continue;
            $range = $values[$k];
            if (strstr($range, "$min - $max") === false) continue;
            $url = $v;
        }
        $I->amOnUrl($url);
        $I->waitForElement(self::$itemsPrice);
        $prices = $I->grabMultiple(self::$itemsPrice);
        foreach ($prices as $price) {
            $price = str_replace("$", "", $price);
            $I->assertGreaterThanOrEqual($min, $price);
            $I->assertLessOrEquals($max, $price);
        }
    }

    public function checkFilterByPromo()
    {
        $I = $this->tester;
        list($urls,) = $this->getFilters();
        $url = "";
        foreach ($urls as $k => $v) {
            if (strstr($v, self::$filterPromoOnSale) === false) continue;
            $url = $v;
        }
        $I->amOnUrl($url);
        $I->waitForElement(self::$itemsPrice);
        $onSaleItems = $I->grabMultiple(self::$onSale);
        $I->assertLessOrEquals(count($onSaleItems), self::DEFAULT_RESULTS_ON_PAGE);
    }

    public function checkFilterByBrand($brand)
    {
        $I = $this->tester;
        list($urls,) = $this->getFilters();
        $url = "";
        foreach ($urls as $k => $v) {
            if (strstr($v, self::$filterByBrand) === false) continue;
            if (strstr($v, self::$brandMap[$brand]) === false) continue;
            $url = $v;
        }
        $I->amOnUrl($url);
        $I->waitForElement(self::$itemsPrice);
        $itemsBrand = $I->grabMultiple(self::$brandName);
        $I->assertLessOrEquals(self::DEFAULT_RESULTS_ON_PAGE, count($itemsBrand));
        foreach ($itemsBrand as $itemBrand) {
            $I->assertEquals($brand, $itemBrand);
        }
    }

    public function checkFilterByRating($min, $max)
    {
        $I = $this->tester;
        list($urls, $values) = $this->getFilters();
        $url = "";
        foreach ($urls as $k => $v) {
            if (strstr($v, self::$filterByRating) === false) continue;
            $range = $values[$k];
            if (strstr($range, "$min - $max") === false) continue;
            $url = $v;
        }
        $I->amOnUrl($url);
        $I->waitForElement(self::$itemsPrice);
        $ratings = $I->grabMultiple(self::$rating);
        $I->assertLessOrEquals(self::DEFAULT_RESULTS_ON_PAGE, count($ratings));
        foreach ($ratings as $rating) {
            $rating = preg_replace("#\\D+#", "", $rating);
            $I->assertGreaterThanOrEqual($min, $rating);
            $I->assertLessOrEquals($max, $rating);
        }
    }

    public function checkSearch($brand)
    {
        $I = $this->tester;
        $I->fillField(self::$searchField, $brand);
        $I->waitAndClick(self::$searchButton);
        $I->waitForElement(self::$brandName);
        $itemsBrand = $I->grabMultiple(self::$brandName);
        foreach ($itemsBrand as $itemBrand) {
            $I->assertContains(strtolower($brand), strtolower($itemBrand));
        }
    }

    protected function getFilters()
    {
        $I = $this->tester;
        $urls = $I->grabMultiple(self::$filters, "href");
        $values = $I->grabMultiple(self::$filters);
        return [$urls, $values];
    }
}
