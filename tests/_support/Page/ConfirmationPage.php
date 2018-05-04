<?php

namespace Page;

use Exception;

class ConfirmationPage
{
    protected $tester;

    public static $orderId = "//*[contains(text(), 'ORDER:')]/..";
    public static $logOutButton = '//*[@class="btn btn-default btn-logout"]';
    public static $thankYouText = '//*[@class="col-xs-12"]';

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function checkLogoutButtonAndClickToResetIdleTime()
    {
        $I = $this->tester;
        $I->wait(15);
        $I->seeElement(self::$logOutButton);
        $I->click(self::$thankYouText);
    }

    public function getOrderId()
    {
        $I = $this->tester;
        try {
            $order_id = $I->grabTextFrom(self::$orderId);
            $order_id = trim($order_id, "ORDER: ");
        } catch (Exception $e) {
            $order_id = false;
        }
        return $order_id;
    }

    public function check500()
    {
        $I = $this->tester;
        $I->dontSeeInTitle("500 - Internal server error.");
    }

    public function seeLinksAre200InEmail($email_text)
    {
        $I = $this->tester;
        $links = $this->grabLinksFromText($email_text);
        foreach ($links as $link) {
            $resp = \SimpleRestClient::get($link, [], [], true, true, "", "", [], true);
            if (isset($resp['http_code']) && $resp['http_code'] != 200) {
                $I->fail("$link returns bad status code: {$resp['http_code']}");
            }
        }
    }

    public function checkAddressInEmail($expected_address_data, $email_text)
    {
        $I = $this->tester;
        $address_arr = explode("\n", $expected_address_data);
        array_pop($address_arr);
        for ($i = 0; $i < count($address_arr) - 2; $i++) {
            $I->assertContains($address_arr[$i], $email_text);
        }
    }

    public function assertOrderOnHoldInEmail($email_text)
    {
        $I = $this->tester;
        $on_hold_txt = 'Order on Hold - Age Verification Required';
        $I->assertContains($on_hold_txt, $email_text);
    }

    public function checkOrderPriceInEmail($price, $email_text)
    {
        $I = $this->tester;
        $I->assertContains($price['total'], $email_text);
        $I->assertContains($price['subtotal'], $email_text);
        $I->assertContains($price['shipping_price'], $email_text);
    }

    public function checkOrderItems($items, $email_text)
    {
        $I = $this->tester;
        $items = explode("\n", $items);
        foreach ($items as $item) {
            if (preg_match("#.*(?=\\()#", $item, $matches)) {
                if (empty($matches[0])) continue;
                $I->assertContains($matches[0], $email_text);
                continue;
            }
            if (preg_match("#\\$.*$#", $item, $matches)) {
                if (empty($matches[0])) continue;
                $I->assertContains(str_replace("$", "", $matches[0]), $email_text);
            }
        }
    }

    public function checkCardNumInEmail($num, $email_text)
    {
        $I = $this->tester;
        $num = substr($num, -4);
        $I->assertContains($num, $email_text);
    }

    public function checkShippingMethodInEmail($shipping_method, $email_text)
    {
        $I = $this->tester;
        $I->assertContains($shipping_method, $email_text);
    }

    private function grabLinksFromText($text)
    {
        $array = [];
        $regex = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
        preg_match_all($regex, $text, $matches);
        // go over all links
        foreach ($matches[0] as $url) {
            $array[] = $url;
        }
        return $array;
    }
}


