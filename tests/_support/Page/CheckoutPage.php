<?php
namespace Page;

use \Exception;

class CheckoutPage
{
    // should be passed in wrapped methods to fix practitest fails
    const PRACTITEST_FIX = "practitest_fix";

    // Checkout Page
    public static $checkoutURL = '/checkout?displayMobile=false';
    public static $title = '//h1';
    public static $enterYourEmailField = './/*[@id="loginform"]/div[1]/input';
    public static $newUserCheckbox = '//*[@id="newreg"]';
    public static $existUserCheckbox = '//*[@id="exist"]';
    public static $existUserPassField = '//*[@class="notnew"]';
    public static $signInButton = './/*[@id="login-submit-button"]/button';
    public static $shoppingCartLocator = '//*[@class="cartbigcol"]/div[2]';
    public static $shoppingCart = '//*[@class="cart_title desktop tablet"]';
    public static $addCartButton = '//*[@id="add_cc_btn"]';
    public static $addPayPalButton = '#add_pp_btn';
    public static $subscribeCheckbox = '//*[@ for="sendcat"]';
    public static $giftCheckbox = "//*[@id='giftcheckbox']";
    public static $giftMessageText = "//*[@id='giftmsg']";
    public static $waitingSpinner = "//*[@class='modalspinner']";
    public static $removeCardButton = "//*[@data-modal='removeToken']";
    public static $confirmRemovingCartButton = "//*[@id='removeCC']";
    public static $billingAddress = "//*[@id='billingaddresscontent']";
    public static $shippingAddress = "//*[@id='shippingaddresscontent']";
    public static $orderItems = "//*[@id='ordersummarycontent']/div/div[contains(@class, 'page-header')]";
    public static $orderSubtotal = ".//*[@id='ordersummarycontent']/div/div[2]/div/div[1]/div[2]";
    public static $orderShipping = ".//*[@id='ordersummarycontent']/div/div[2]/div/div[2]/div[2]";
    public static $orderTotal = ".//*[@id='ordersummary_grandtotal']";
    public static $selectedShipping = "//*[@checked='checked']/../following-sibling::div[1]";
    //add card
    public static $addFirstCardBlock = ".//*[@id='addfirstcard']/*/*/*/*[@id='cardmodalbody']/*[@id='addcardarea']";
    public static $addNewCardBlock = ".//*[@id='addnewcard']/*/*[@id='cardmodalbody']/*[@id='addcardarea']";
    public static $cardNumberField = '#cc-number';
    public static $mmyy = '//*[@id="cc-exp"]';
    public static $cvv = '//*[@id="cc-cvv"]';
    public static $submitButton = '//*[@id="submitcard"]';
    public static $placeOrderButton = '//*[@id="btn-place-order"]';
    public static $notificationError = './/*[@id="paymentmethodcontent"]/div/div';
    //PayPal Page
    public static $paypalLocator = '//*[@id="defaultCancelLink"]';
    //credit card checkout page
    public static $testCardAdded = "//*[@class='tokencontainer']";
    public static $cardCheckbox = '//*[@class="radio-cust-display"]';
    public static $cardCvvField = '//*[@class="input-group cvvarea"]/input[1]';
    //shipping methods
    public static $shippingMethodsMenu = "//*[@id='shippingmethodcontent']";
    public static $shippingPrice = "//*[contains(., '%s')]/../following-sibling::div/span";
    public static $shippingPriceOrderSummary = "//*[contains(text(), 'Shipping and handling')]/following-sibling::*";
    public static $itlShippingCheckbox = "//*[@id='intshipwaiverbox']";

    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function signInExistUser($email, $pass){
        $I = $this->tester;
        $I->fillField(self::$enterYourEmailField,$email);
        $I->waitAndClick(self::$existUserCheckbox);
        $I->fillField(self::$existUserPassField,$pass);
        $I->dontSee("The email or password you entered is incorrect. Please check the spelling and try again.");
        $I->waitAndClick(self::$signInButton);
        $I->wait(1);
        $I->dontSee("The email or password you entered is incorrect. Please check the spelling and try again.");
    }

    protected function checkout($cardNumber, $data, $cv, $shippingType, $addNewCard = false){
        $I = $this->tester;
        $I->wait(1);
        $I->amOnPage(self::$checkoutURL);
        $I->waitForElementVisible(self::$subscribeCheckbox);
        $I->scrollTo(self::$subscribeCheckbox);
        $I->waitAndClick(self::$subscribeCheckbox);
        $price = $this->fillShippingData($shippingType);
        $this->fillCCData($cardNumber, $data, $cv, $addNewCard);
        if (!empty($price)) {
            // check shipping price at Order Summary
            $I->waitForElementNotVisible(self::$waitingSpinner);
            $I->waitForElementVisible(self::$shippingPriceOrderSummary);
            $I->see($price, self::$shippingPriceOrderSummary);
        }
        $bil_add = $this->getBillingAddress();
        $shipp_add = $this->getShippingAddress();
        $items = $this->getOrderItems();
        $order_price = $this->getOrderPrice();
        $shipping_method = $this->getSelectedShippingMethod();
        return [
            "billing_address" => $bil_add,
            "shipping_address" => $shipp_add,
            "items" => $items,
            "price" => $order_price,
            "shipping_method" => $shipping_method
        ];
    }

    public function checkoutCorrectData($cardNumber, $data, $cv, $shippingType, $need_data = false)
    {
        $I = $this->tester;
        $data = $this->checkout($cardNumber, $data, $cv, $shippingType);
        $I->waitAndClick(self::$placeOrderButton);
        $this->checkSuccessfulOrder();
        if ($need_data) return $data;
    }

    public function checkoutAsGift($cardNumber, $data, $cv, $shippingType, $gift_text)
    {
        $I = $this->tester;
        $this->checkout($cardNumber, $data, $cv, $shippingType);
        $I->waitForElementVisible(self::$giftCheckbox);
        $I->waitAndClick(self::$giftCheckbox);
        $I->fillField(self::$giftMessageText, $gift_text);
        $I->waitAndClick(self::$placeOrderButton);
        $this->checkSuccessfulOrder();
    }

    public function checkoutBadCC($cardNumber, $data, $cv, $shippingType, $error)
    {
        $I = $this->tester;
        $this->checkout($cardNumber, $data, $cv, $shippingType, true);
        try {
            $I->waitForElementVisibleWrap(self::$addNewCardBlock, null, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->waitAndClick(self::$placeOrderButton);
        }
        try {
            $I->waitForTextWrap($error, 3, null, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            $I->waitForText("Unable to save payment method. Please check your payment information and try again.", 3);
        }
    }

    protected function checkSuccessfulOrder()
    {
        $I = $this->tester;
        $final_text = 'YOUR ORDER HAS BEEN PLACED';
        $I->dontSee("Unable to save payment method. Please check your payment information and try again.");
        $I->waitForText($final_text, 60);
        $I->canSee($final_text, self::$title);
        $I->wait(1);
    }

    protected function fillShippingData($shippingType)
    {
        $price = "";
        $I = $this->tester;
        try {
            // intl shipping method
            $I->seeWrap("Shipping options may be restricted because you are shipping outside of the United States", null, self::PRACTITEST_FIX);
            $I->waitForElementVisibleWrap(self::$itlShippingCheckbox, null, self::PRACTITEST_FIX);
            $I->waitAndClickWrap(self::$itlShippingCheckbox, self::PRACTITEST_FIX);
        } catch (Exception $e) {
            // domestic shipping method
            try {
                $I->seeWrap("Shipping options may be restricted because your cart contains the following item", null, self::PRACTITEST_FIX);
            } catch (Exception $e) {
                $I->selectOption(self::$shippingMethodsMenu, $shippingType);
                $price = $I->grabTextFrom(sprintf(self::$shippingPrice, $shippingType));
            }
        }

        return $price;
    }

    protected function fillCCData($cardNumber, $data, $cv, $addNewCard = false)
    {
        $I = $this->tester;
        try {
            // if cc already added
            $I->waitForElementVisibleWrap(self::$testCardAdded, null, self::PRACTITEST_FIX);
            if ($addNewCard) {
                $I->waitForElementVisibleWrap(self::$addCartButton, null, self::PRACTITEST_FIX);
                $I->waitAndClickWrap(self::$addCartButton, self::PRACTITEST_FIX);
                $I->waitForElementVisibleWrap(self::$addNewCardBlock, null, self::PRACTITEST_FIX);
                $I->fillField(self::$cardNumberField,$cardNumber);
                $I->fillField(self::$mmyy,$data);
                $I->fillField(self::$cvv,$cv);
                $I->waitAndClickWrap(self::$submitButton, self::PRACTITEST_FIX);
                $I->waitForElementNotVisibleWrap(self::$waitingSpinner, null, self::PRACTITEST_FIX);
            } else {
                $I->waitAndClickWrap(self::$cardCheckbox, self::PRACTITEST_FIX);
                $I->fillField(self::$cardCvvField,$cv);
            }
        } catch (Exception $e) {
            // fill cc data otherwise
            $I->fillField(self::$cardNumberField,$cardNumber);
            $I->fillField(self::$mmyy,$data);
            $I->fillField(self::$cvv,$cv);
        }
    }

    protected function getBillingAddress()
    {
        $I = $this->tester;
        $billign_address = $I->grabTextFrom(self::$billingAddress);
        return $billign_address;
    }

    protected function getShippingAddress()
    {
        $I = $this->tester;
        $shipping_address = $I->grabTextFrom(self::$shippingAddress);
        return $shipping_address;
    }

    protected function getOrderItems()
    {
        $I = $this->tester;
        $items = $I->grabTextFrom(self::$orderItems);
        return $items;
    }

    protected function getOrderPrice()
    {
        $I = $this->tester;
        $subtotal = $I->grabTextFrom(self::$orderSubtotal);
        $shipping_price = $I->grabTextFrom(self::$orderShipping);
        $total = $I->grabTextFrom(self::$orderTotal);
        return [
            "subtotal" => $subtotal,
            "total" => $total,
            "shipping_price" => $shipping_price,
        ];
    }

    protected function getSelectedShippingMethod()
    {
        $I = $this->tester;
        $selected_shipp_meth = $I->grabTextFrom(self::$selectedShipping);
        return $selected_shipp_meth;
    }
}
