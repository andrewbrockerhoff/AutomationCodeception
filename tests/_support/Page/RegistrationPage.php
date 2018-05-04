<?php
namespace Page;

class RegistrationPage
{
    public static $URL = '/?displayMobile=false';
    public static $RegistrationURL = '/register?displayMobile=false';

    public static $title = '//h1';
    public static $countryDropDownList = './/*[@id="country"]';
    // Billing Address
    public static $firstNameField = '//*[@name="fname"]';
    public static $lastNameField = '//*[@name="lname"]';
    public static $companyField = '//*[@name="company"]';
    public static $address1Field = '//*[@name="adr1"]';
    public static $address2Field = '//*[@name="adr2"]';
    public static $cityField = '//*[@name="city"]';
    public static $stateDropDownList = '//*[@name="state"]';
    public static $zipCodeField = '//*[@name="zip"]';
    public static $phoneNumberField = '//*[@name="phone"]';
    public static $cellNumberField = './/*[@id="phone2"]';
    // Account Information
    public static $emailField = './/*[@id="email"]';
    public static $passField = '//*[@name="pass_confirmation"]';
    public static $reTypePassField = '//*[@name="pass"]';
    public static $sendToEmailCheckbox = '//*[@class="nobordered"]/*[@class="checkbox-custom-label"]';
    // Age Verification
    public static $BdayField = '//*[@autocomplete="bday"]';
    public static $termsAndConditionsCheckbox = '//*[@class="bordered"]/*[@class="checkbox-custom-label"]';
    public static $reCapthaField = '.recaptcha-checkbox-borderAnimation';
    public static $registrationAccountButton = '//*[@id="submitRegistration"]';
    public static $welcomeRegistrationMessage = '//*[@id="registrationComplete"]';
    public static $accountHeaderButton = './/*[@class="pagebar"]/div[1]/div[2]/a';
    public static $logoutButton = '//a[text()="Logout"]';
    public static $logo = '//*[@class="logo"]';
    public static $emailSignInField = '//*[@class="headlogin"]/input[2]';
    public static $passSignInField = '//*[@class="headlogin"]/input[3]';
    public static $signInButton = '//*[@class="headlogin"]/button';
    public static $addressValidationRadioButton = '//*[@class="col-xs-1"]/input';
    public static $selectedAddressButton = '//span[contains(text(),"Use Selected Address")]';

    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public function goToRegistrationPage()
    {
        $I = $this->tester;
        $I->amOnPage(self::$RegistrationURL);
        $I->see('Create An Account', self::$title);
    }

    public function signUp(
        $firstName,
        $lastName,
        $address1,
        $city,
        $state,
        $zipCode,
        $phone,
        $email,
        $pass,
        $bDay
    ) {
        $I = $this->tester;
        $I->fillField(self::$BdayField, $bDay);
        $I->waitAndClick(self::$emailField);
        $I->fillField(self::$emailField, $email);
        $I->fillField(self::$passField, $pass);
        $I->fillField(self::$reTypePassField, $pass);
        $I->fillField(self::$firstNameField, $firstName);
        $I->fillField(self::$lastNameField, $lastName);
        $I->fillField(self::$address1Field, $address1);
        $I->fillField(self::$cityField, $city);
        $I->selectOption(self::$stateDropDownList, $state); // 'PA'
        $I->fillField(self::$zipCodeField, $zipCode);
        $I->fillField(self::$phoneNumberField, $phone);
        $I->dontSee('Please confirm your password matches');
        $I->canSeeCheckboxIsChecked('#signup');
        $I->waitAndClick(self::$registrationAccountButton);
        $I->waitForElementVisible(self::$selectedAddressButton);
        $I->waitAndClick(self::$addressValidationRadioButton);
        $I->wait(10);
        $I->waitAndClick(self::$selectedAddressButton);
        $I->waitForElementVisible(self::$welcomeRegistrationMessage);
        $I->see('Welcome and thank you for joining our family of Famous Smokers.', self::$welcomeRegistrationMessage);
    }

    public function logout()
    {
        $I = $this->tester;
        $I->waitAndClick(self::$logo);
        $I->waitForElementVisible(self::$accountHeaderButton);
        $I->waitAndClick(self::$accountHeaderButton);
        $I->waitForElementVisible(self::$logoutButton);
        $I->waitAndClick(self::$logoutButton);
    }

    public function signIn($email, $pass)
    {
        $I = $this->tester;
        $I->amOnPage(self::$URL);
        $I->waitAndClick(self::$accountHeaderButton);
        $I->waitForElementVisible(self::$emailSignInField);
        $I->fillField(self::$emailSignInField, $email);
        $I->fillField(self::$passSignInField, $pass);
        $I->waitAndClick(self::$signInButton);
        $I->waitAndClick(self::$accountHeaderButton);
        $I->waitForElementVisible(self::$logoutButton);
        $I->waitAndClick(self::$accountHeaderButton);
    }

    public function see($text, $selector = null)
    {
        $I = $this->tester;
        $I->see($text, $selector);
    }

    public function fillBirthday($date)
    {
        $I = $this->tester;
        $I->fillField(self::$BdayField, $date);
    }

    public function fillEmail($email)
    {
        $I = $this->tester;
        $I->waitAndClick(self::$emailField);
        $I->fillField(self::$emailField, $email);
    }

    public function fillPassword($pass)
    {
        $I = $this->tester;
        $I->fillField(self::$passField, $pass);
    }

    public function fillReTypePass($retype_pass)
    {
        $I = $this->tester;
        $I->fillField(self::$reTypePassField, $retype_pass);
    }

    public function fillFirstName($first_name)
    {
        $I = $this->tester;
        $I->fillField(self::$firstNameField, $first_name);
    }

    public function fillLastName($last_name)
    {
        $I = $this->tester;
        $I->fillField(self::$lastNameField, $last_name);
    }

    public function fillAddress($address)
    {
        $I = $this->tester;
        $I->fillField(self::$address1Field, $address);
    }

    public function fillCity($city)
    {
        $I = $this->tester;
        $I->fillField(self::$cityField, $city);
    }

    public function fillZip($zip)
    {
        $I = $this->tester;
        $I->fillField(self::$zipCodeField, $zip);
    }

    public function selectState($state)
    {
        $I = $this->tester;
        $I->selectOption(self::$stateDropDownList, $state);
    }

    public function fillPhoneNum($phone)
    {
        $I = $this->tester;
        $I->fillField(self::$phoneNumberField, $phone);
    }

    public function submitRegistration()
    {
        $I = $this->tester;
        $I->waitAndClick(self::$registrationAccountButton);
    }

    public function waitForText($text)
    {
        $I = $this->tester;
        $I->waitForText($text);
    }
}
