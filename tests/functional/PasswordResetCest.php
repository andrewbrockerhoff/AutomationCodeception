<?php

class PasswordResetCest
{
	public $auth = [
            'key' => 'key-62e85c11e102a0bfaa4f9c5b76ed3238',
			'username' => 'api',
			'email' => 'reset@mg.brock-design.com',
			'newpass' => 'test12345!'
    ];

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

	public function testResetEmail(FunctionalTester $I)
	{
		$I->wantTo('check that reset password email contains successfull links with 200 ok');
		$this->fillResetPasswordForm($I);
		$emailUrl = $this->grabEmailUrl($I);
		$text = $this->grabEmailMessage($I, $emailUrl);
		$links = $this->grabLinksFromText($text);
		$resetLink = $this->getResetPassLink($links);
		$this->seeLinksAre200($I, $links);
		$this->submitNewPasswordForm($I, $resetLink);
		$this->checkNewPassword($I);
	}

	private function fillResetPasswordForm(FunctionalTester $I)
	{
		$I->amOnUrl('https://www.famous-smoke.com/reset-password');
		$I->fillField('input#email', $this->auth['email']);
		$I->click('input.newbtn.forgotten');
		// waiting for reset password email being sent
		sleep(30);
	}

	private function grabEmailUrl(FunctionalTester $I )
	{
		$params = [
			'limit' => '1',
			'event' => 'stored',
			'from' => 'customerservice@famous-smoke.com',
			'to' => $this->auth['email']
		];
		$I->amOnUrl('https://api.mailgun.net/v3/mg.brock-design.com/events');
		$I->amHttpAuthenticated($this->auth['username'], $this->auth['key']);
		$I->sendGET('', $params);
		$I->seeResponseCodeIs(200);
		$json = json_decode($I->grabResponse());
		return $json->items[0]->storage->url;
	}

	private function grabEmailMessage(FunctionalTester $I, $url)
	{
		$I->sendGET($url);
		$I->amHttpAuthenticated($this->auth['username'], $this->auth['key']);
		$I->seeResponseCodeIs(200);
		$json = json_decode($I->grabResponse());
		$stripped = 'stripped-text';
		return $json->$stripped;
	}

	private function grabLinksFromText($text)
	{
		$regex = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
		preg_match_all($regex, $text, $matches);
		// go over all links
		foreach($matches[0] as $url) {
    			$array[] = $url;
		}
		return $array;
	}

	private function seeLinksAre200(FunctionalTester $I, $links)
	{
		foreach ($links as $link) {
			if (strstr($link, 'update-password') != true) {
				$I->amOnUrl($link);
				$I->seeResponseCodeIs(200);
			}
		}
	}

	private function getResetPassLink($links)
	{
		foreach ($links as $link) {
			if (strstr($link, 'update-password') == true) {
				return $link;
			}
		}
	}

	private function submitNewPasswordForm(FunctionalTester $I, $url)
	{
	    $tmp = explode("/", $url);
		$key = end($tmp);
		$I->amOnUrl($url);
		$I->see('Update Your Password');
		$I->sendPOST('/update-password', 
			[
			    'submitted' => '1',
			    'data' => $key,
			    'pwd' => $this->auth['newpass'],
			    'pwd2' => $this->auth['newpass']
            ]
		);
		$I->seeResponseCodeIs(200);
		$response = $I->grabResponse();
		$I->assertContains('Password updated successfully.', $response);
	}

	private function checkNewPassword(FunctionalTester $I)
	{
		$I->amOnUrl('https://www.famous-smoke.com/');
		$I->sendPOST(
		    '/api/auth/login',
			[
			    'homeurl' => '//www.famous-smoke.com',
                'defaulturl' => '/',
                'guesturl' => 'register',
                'uname' => $this->auth['email'],
                'logtype' => 'exist',
                'pwd' => $this->auth['newpass']
		    ]
        );
		$I->seeResponseCodeIs(200);
		$json = json_decode($I->grabResponse());
		$I->assertTrue($json->STATUS);
	}
}

