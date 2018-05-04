<?php

class RegistrationTestCest
{
    const DEFAULT_PASS = '!1qwerty';
    const MIN_PASS_LEN = 8;
    const MAX_PATH_LEN = 16;

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
     * @group registration
     */
    function Registration(\Page\RegistrationPage $registrationPage)
    {

        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->signUp(
            $faker->firstName,
            $faker->lastName,
            $faker->streetAddress,
            $faker->city,
            $faker->state,
            $faker->postcode,
            $faker->tollFreePhoneNumber,
            'test.' . $faker->email,
            self::DEFAULT_PASS,
            $faker->date('m/d/Y', '-22 years')
        );
    }

    /**
     * @group resetpass
     * @skip
     */
    function ResetForgottenPassword(\Page\ResetPasswordPage $resetPasswordPage, \Step\Acceptance\EmailSteps $emailSteps)
    {
        $resetPasswordPage->goToResetPasswordPage();
        $resetPasswordPage->enterTestEmail('famoussmokeshoptester@yahoo.com', 'reset');
        $emailSteps->loginEmailYahoo();
        $resetPasswordPage->enterNewPassword(self::DEFAULT_PASS);
    }

    /**
     * @group empty_pass
     */
    function EmptyPassword(\Page\RegistrationPage $registrationPage)
    {
        $empty_pass = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword($empty_pass);
        $registrationPage->fillReTypePass($empty_pass);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter a password.");
        $registrationPage->see("Please re-enter your password.");
    }

    /**
     * @group empty_email
     */
    function EmptyEmail(\Page\RegistrationPage $registrationPage)
    {
        $empty_email = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail($empty_email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter a valid e-mail");
    }

    /**
     * @group diff_pass_and_retype
     */
    function DifferentPassAndRetypePass(\Page\RegistrationPage $registrationPage)
    {
        $faker = Faker\Factory::create('en_US');
        $retype_pass = $faker->password(self::MIN_PASS_LEN, self::MAX_PATH_LEN);
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass($retype_pass);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please confirm your password matches.");
    }

    /**
     * @group short_pass
     */
    function ShortPassword(\Page\RegistrationPage $registrationPage)
    {
        $faker = Faker\Factory::create('en_US');
        $short_pass = $faker->password(self::MIN_PASS_LEN - 1, self::MIN_PASS_LEN - 1);
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword($short_pass);
        $registrationPage->fillReTypePass($short_pass);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter a password between 8 and 15 characters.");
    }

    /**
     * @group long_pass
     */
    function LongPassword(\Page\RegistrationPage $registrationPage)
    {
        $faker = Faker\Factory::create('en_US');
        $short_pass = $faker->password(self::MAX_PATH_LEN + 1, self::MAX_PATH_LEN + 1);
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword($short_pass);
        $registrationPage->fillReTypePass($short_pass);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter a password between 8 and 15 characters.");
    }

    /**
     * @group bad_email
     */
    function BadEmail(\Page\RegistrationPage $registrationPage)
    {
        $faker = Faker\Factory::create('en_US');
        $bad_email = $faker->text();
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail($bad_email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter a valid e-mail");
    }

    /**
     * @group empty_first_name
     */
    function EmptyFirstName(\Page\RegistrationPage $registrationPage)
    {
        $empty_first_name = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($empty_first_name);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter your first name.");
    }

    /**
     * @group empty_last_name
     */
    function EmptyLastName(\Page\RegistrationPage $registrationPage)
    {
        $empty_last_name = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($empty_last_name);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter your last name.");
    }

    /**
     * @group empty_address
     */
    function EmptyAddress(\Page\RegistrationPage $registrationPage)
    {
        $empty_address = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($empty_address);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter your street address.");

    }

    /**
     * @group empty_city
     */
    function EmptyCity(\Page\RegistrationPage $registrationPage)
    {
        $empty_city = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($empty_city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter your city.");

    }

    /**
     * @group empty_zip
     */
    function EmptyZip(\Page\RegistrationPage $registrationPage)
    {
        $empty_zip = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($empty_zip);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter your zip code.");
    }

    /**
     * @group bad_zip
     */
    function BadZip(\Page\RegistrationPage $registrationPage)
    {
        $faker = Faker\Factory::create('en_US');
        $bad_zip = $faker->text();
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($bad_zip);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter a valid zip code.");
    }

    /**
     * @group empty_phone
     */
    function EmptyPhone(\Page\RegistrationPage $registrationPage)
    {
        $empty_phone = "";
        $faker = Faker\Factory::create('en_US');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($faker->date('m/d/Y', '-21 years'));
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($empty_phone);
        $registrationPage->submitRegistration();
        $registrationPage->see("Please enter your phone number.");
    }

    /**
     * @group bad_birthday
     */
    function WrongBirthDate(\Page\RegistrationPage $registrationPage)
    {
        $faker = Faker\Factory::create('en_US');
        $bad_birth = $faker->dateTimeBetween('-20 years', '-20 years')->format('m/d/Y');
        $registrationPage->goToRegistrationPage();
        $registrationPage->fillBirthday($bad_birth);
        $registrationPage->fillEmail('test.' . $faker->email);
        $registrationPage->fillPassword(self::DEFAULT_PASS);
        $registrationPage->fillReTypePass(self::DEFAULT_PASS);
        $registrationPage->fillFirstName($faker->firstName);
        $registrationPage->fillLastName($faker->lastName);
        $registrationPage->fillAddress($faker->streetAddress);
        $registrationPage->fillCity($faker->city);
        $registrationPage->selectState($faker->state);
        $registrationPage->fillZip($faker->postcode);
        $registrationPage->fillPhoneNum($faker->tollFreePhoneNumber);
        $registrationPage->submitRegistration();
        $registrationPage->see("You must be 21 or over to register.");
    }
}

