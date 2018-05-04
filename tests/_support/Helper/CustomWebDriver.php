<?php

namespace Helper;

use Codeception\Exception\ConnectionException;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class CustomWebDriver extends \Codeception\Module\WebDriver
{
    const
        CHROME = 'chrome',
        FIREFOX = 'firefox';
    protected $user_agent = '';

    public function _initialize()
    {
        parent::_initialize();
        if (isset($this->config['user-agent'])) {
            $this->user_agent = $this->config['user-agent'];
        }
    }

    public function _initializeSession()
    {
        if (empty($this->user_agent)) {
            return parent::_initializeSession();
        }
        $capabilities = $this->prepareUserAgent($this->user_agent);
        try {
            $this->sessions[] = $this->webDriver;
            $this->webDriver = \RemoteWebDriver::create(
                $this->wdHost,
                $capabilities,
                $this->connectionTimeoutInMs,
                $this->requestTimeoutInMs,
                $this->httpProxy,
                $this->httpProxyPort
            );
            if (!is_null($this->config['pageload_timeout'])) {
                $this->webDriver->manage()->timeouts()->pageLoadTimeout($this->config['pageload_timeout']);
            }
            $this->setBaseElement();
            $this->initialWindowSize();
        } catch (\WebDriverCurlException $e) {
            throw new ConnectionException("Can't connect to Webdriver at {$this->wdHost}. Please make sure that Selenium Server or PhantomJS is running.");
        }
    }

    protected function prepareUserAgent($user_agent)
    {
        $capabilities = $this->capabilities;
        if ($this->config['browser'] == self::CHROME) {
            $opts = new ChromeOptions();
            $opts->addArguments(['--user-agent=' . $user_agent]);
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $opts);
        }
        if ($this->config['browser'] == self::FIREFOX) {
            $profile = new FirefoxProfile();
            $profile->setPreference('general.useragent.override', $user_agent);
            $capabilities = DesiredCapabilities::firefox();
            $capabilities->setCapability(FirefoxDriver::PROFILE, $profile);
        }

        return $capabilities;
    }
}
