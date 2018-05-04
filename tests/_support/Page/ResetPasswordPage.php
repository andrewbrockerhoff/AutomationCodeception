<?php
namespace Page;

use Exception;

class ResetPasswordPage
{
    protected $tester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    public static $ResetPasswordURL = '/reset-password?displayMobile=false';

    public static $title = '//h2';
    public static $notificationMessage = '//h3';
// Billing Address
    public static $emailField = '//*[@id="email"]';
    public static $submitButton = '//*[@type="submit"]';



    public function goToResetPasswordPage(){
        $I = $this->tester;
        $I->amOnPage(self::$ResetPasswordURL);
        $I->wait(10);
        $I->waitForElement(self::$title,20);
        $I->see('Reset Your Password',self::$title);
    }

    public function enterTestEmail($email,$error1)    {
        $I = $this->tester;
        $I->fillField(self::$emailField, $email);
        $I->waitAndClick(self::$submitButton);
        $I->canSee($error1, self::$notificationMessage);
        }

 // Forgotten Password Page
    public static $newPasswordField = '//*[@id="pwd"]';
    public static $updatePasswordField = './/*[@id="updatepasswordform"]/div[2]/input';
    public static $submitForgottenButton = './/*[@value="Submit"]';

    public function enterNewPassword($pass)    {
        $I = $this->tester;
        $I->waitForElement(self::$newPasswordField);
        $I->fillField(self::$newPasswordField, $pass);
        $I->fillField(self::$updatePasswordField, $pass);
        $I->waitAndClick(self::$submitForgottenButton);
        $I->wait(5);
        $I->see('successfully');

    }

    public static $resetPasswordTestEmailURL = '/update-password/h469kifC0@252F@1JL8Q2Ipo5gV@252B@WaxyCDXIbzt9dgGrh0wASHnKDsKmoxnB@252F@cnAAz9cwb6k0aDTGAfMQokdg17rxHQ';
    public function passwordResetLink ($pass){
        $I = $this->tester;
        $I->amOnPage(self::$resetPasswordTestEmailURL);
        $I->waitForElement(self::$newPasswordField);
        $I->fillField(self::$newPasswordField, $pass);
        $I->fillField(self::$updatePasswordField, $pass);
        $I->waitAndClick(self::$submitForgottenButton);
        $I->see('Password updated','//h3');
    }
}

