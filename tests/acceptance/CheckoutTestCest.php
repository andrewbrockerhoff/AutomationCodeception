<?php

use \Step\Acceptance;

class CheckoutTestCest
{
    const DEFAULT_CC_NUM = "4444-3333-2222-1111";
    const DEFAULT_CC_EXP = "12\\19";
    const DEFAULT_CC_CVC = "123";
    const DEFAULT_EMAIL = "abrocker@gmail.com";
    const DEFAULT_PASS = 'test12345!';
    const CHECKOUT_EMAIL_DELAY_SEC = 300;

    protected $steps = [];

    protected static $domesticShippingTypes = [
        "Economy"
    ];

    protected $auth = [
        'key' => 'key-62e85c11e102a0bfaa4f9c5b76ed3238',
        'username' => 'api',
        'email' => 'reset@mg.brock-design.com',
        'newpass' => 'test12345!'
    ];

    /**
     * @return array
     */
    protected function providerForTestCheckoutWithExistingAccount()
    {
        return [
            ["email" => self::DEFAULT_EMAIL, "pass" => self::DEFAULT_PASS], //domestic
            ["email" => "abrocker@famous-smoke.com", "pass" => "test12345!"], //intl
        ];
    }

    /**
     * Run after every scenario
     */
    public function _after(AcceptanceTester $I)
    {
        $I->sendResultToPractiTest();
    }

    public function _failed(AcceptanceTester $I)
    {
        $I->sendResultToPractiTest();
    }

    /**
     * @group checkout
     * @dataProvider providerForTestCheckoutWithExistingAccount
     */
    public function CheckoutWithExistingAccount(\Page\ItemsPage $itemsPage, \Page\CheckoutPage $checkoutPage, \Codeception\Example $example)
    {
        $faker = Faker\Factory::create('en_US');
        $itemsPage->addRandomItem();
        $itemsPage->addCigarillosItem();
        // $itemsPage->addButaneItem();
        $itemsPage->addLargeHumidorsItem();
        $checkoutPage->signInExistUser($example["email"], $example["pass"]);
        $checkoutPage->checkoutCorrectData(
            self::DEFAULT_CC_NUM,
            self::DEFAULT_CC_EXP,
            self::DEFAULT_CC_CVC,
            $faker->randomElement(self::$domesticShippingTypes)
        );
    }

    /**
     * @group checkout-gift
     */
    function CheckoutAsGift(\Page\ItemsPage $itemsPage, \Page\CheckoutPage $checkoutPage)
    {
        $faker = Faker\Factory::create('en_US');
        $itemsPage->addRandomItem();
        $checkoutPage->signInExistUser(self::DEFAULT_EMAIL, self::DEFAULT_PASS);
        $checkoutPage->checkoutAsGift(
            self::DEFAULT_CC_NUM,
            self::DEFAULT_CC_EXP,
            self::DEFAULT_CC_CVC,
            $faker->randomElement(self::$domesticShippingTypes),
            $faker->text(70)
        );
    }

    /**
     * @group checkout-bad-cc-num
     */
    function CheckoutBadCCNum(\Page\ItemsPage $itemsPage, \Page\CheckoutPage $checkoutPage)
    {
        $badCCNum = [
            "1111111111111111",
            "",
        ];
        $faker = Faker\Factory::create('en_US');
        $itemsPage->addRandomItem();
        $checkoutPage->signInExistUser(self::DEFAULT_EMAIL, self::DEFAULT_PASS);
        $checkoutPage->checkoutBadCC(
            $faker->randomElement($badCCNum),
            $faker->creditCardExpirationDateString,
            $faker->numberBetween(100, 999),
            $faker->randomElement(self::$domesticShippingTypes),
            "Please check credit card number"
        );
    }

    /**
     * @group checkout-bad-cc-exp-date
     */
    function CheckoutBadCCExpDate(\Page\ItemsPage $itemsPage, \Page\CheckoutPage $checkoutPage)
    {
        $badCCExpDate = [
            "00/00",
            "",
        ];
        $faker = Faker\Factory::create('en_US');
        $itemsPage->addRandomItem();
        $checkoutPage->signInExistUser(self::DEFAULT_EMAIL, self::DEFAULT_PASS);
        $checkoutPage->checkoutBadCC(
            $faker->creditCardNumber,
            $badCCExpDate,
            $faker->numberBetween(100, 999),
            $faker->randomElement(self::$domesticShippingTypes),
            "This payment method has already expired."
        );
    }

    /**
     * @group checkout-bad-cc-cvv
     */
    function CheckoutBadCCCvv(\Page\ItemsPage $itemsPage, \Page\CheckoutPage $checkoutPage)
    {
        $badCvv = [
            "",
        ];
        $faker = Faker\Factory::create('en_US');
        $itemsPage->addRandomItem();
        $checkoutPage->signInExistUser(self::DEFAULT_EMAIL, self::DEFAULT_PASS);
        $checkoutPage->checkoutBadCC(
            $faker->creditCardNumber,
            $faker->creditCardExpirationDateString,
            $badCvv,
            $faker->randomElement(self::$domesticShippingTypes),
            "Please check cvv"
        );
    }

    /**
     * @group checkout-email-check
     */
    function CheckoutEmailCheck(\Page\ItemsPage $itemsPage, \Page\CheckoutPage $checkoutPage, \Page\ConfirmationPage $confirmationPage)
    {
        $email = $this->auth["email"];
        $pass = $this->auth["newpass"];
        $itemsPage->addBellaVanillaCigarillo();
        $checkoutPage->signInExistUser($email, $pass);
        $data = $checkoutPage->checkoutCorrectData(
            self::DEFAULT_CC_NUM,
            self::DEFAULT_CC_EXP,
            self::DEFAULT_CC_CVC,
            "Economy",
            true
        );

        $confirmationPage->checkLogoutButtonAndClickToResetIdleTime();
        $orderId = $confirmationPage->getOrderId();
        if (!$orderId) {
            echo "Transaction Number is displayed instead of Order Number\n";
            return;
        }

        // idle for 5 minutes
        for ($i = 1; $i <= 6; $i++) {
            sleep(50);
            $confirmationPage->check500();
        }


        $text = $this->grabEmailMessage($orderId, $confirmationPage);
        $confirmationPage->assertOrderOnHoldInEmail($text);
        $confirmationPage->checkAddressInEmail($data['billing_address'], $text);
        $confirmationPage->checkAddressInEmail($data['shipping_address'], $text);
        $confirmationPage->checkOrderPriceInEmail($data['price'], $text);
        $confirmationPage->checkOrderItems($data['items'], $text);
        $confirmationPage->checkCardNumInEmail(self::DEFAULT_CC_NUM, $text);
        $confirmationPage->checkShippingMethodInEmail($data['shipping_method'], $text);
        $confirmationPage->seeLinksAre200InEmail($text);
    }

    private function grabEmailUrl()
    {
        $params = [
            'limit' => '1',
            'event' => 'stored',
            'from' => 'customerservice@famous-smoke.com',
            'to' => $this->auth['email']
        ];
        $url = 'https://api.mailgun.net/v3/mg.brock-design.com/events';
        $resp = SimpleRestClient::get(
            $url,
            $params,
            [],
            true,
            false,
            $this->auth['username'],
            $this->auth['key']
        );
        $json = json_decode($resp);
        return $json->items[0]->storage->url;
    }

    private function grabEmailMessage($orderId, $confirmationPage)
    {
        $i = 0;
        $start_datetime = time();
        $time_idle_limit = 40;
        $attempts = 100;
        while(true) {
            $url = $this->grabEmailUrl();
            if ($i >= $attempts) {
                throw new \Exception("Unable to grab email for the last order id $orderId");
            }
            $r = SimpleRestClient::get(
                $url,
                [],
                [],
                true,
                false,
                $this->auth['username'],
                $this->auth['key']
            );
            $json = json_decode($r);

            // if time in loop reaches idle limit, send webdriver command to avoid idle timeout
            if ((time() - $start_datetime) >= $time_idle_limit) {
                $start_datetime = time();
                $confirmationPage->check500();
            }

            $stripped = 'stripped-text';
            if (strstr($json->$stripped, $orderId) !== false) {
                $confirmationPage->check500();
                return $json->$stripped;
            }
            $i++;
        }
        return "";
    }
}
