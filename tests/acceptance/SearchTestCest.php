<?php

class SearchTestCest
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
     * @param \Page\SearchPage $searchPage
     * @group search-filters
     */
    public function testFilterByPrice(\Page\SearchPage $searchPage)
    {
        $searchPage->goToSearchPage();
        $searchPage->checkFilterByPrice("10", "19.99");
    }

    /**
     * @param \Page\SearchPage $searchPage
     * @group search-filters
     */
    public function testFilterByPromo(\Page\SearchPage $searchPage)
    {
        $searchPage->goToSearchPage();
        $searchPage->checkFilterByPromo();
    }

    /**
     * @param \Page\SearchPage $searchPage
     * @group search-filters
     */
    public function testFilterByBrand(\Page\SearchPage $searchPage)
    {
        $searchPage->goToSearchPage();
        $searchPage->checkFilterByBrand("ACID");
    }

    /**
     * @param \Page\SearchPage $searchPage
     * @group search-filters
     */
    public function testFilterByRating(\Page\SearchPage $searchPage)
    {
        $searchPage->goToSearchPage();
        $searchPage->checkFilterByRating("70", "79");
    }

    /**
     * @param \Page\SearchPage $searchPage
     * @group search-filters
     */
    public function testSearchByBrand(\Page\SearchPage $searchPage)
    {
        $searchPage->goToSearchPage();
        $searchPage->checkSearch("acid");
        $searchPage->checkSearch("rocky Patel");
        $searchPage->checkSearch("avo");
        $searchPage->checkSearch("swisher");
    }
}
