<?php

namespace Helper;

use Helper\AntiCaptcha;

class CaptchaSolver extends \Codeception\Module
{
    const KEY = "13bdbebfc70009699c3ff855c6b14071";
    const WEB_SITE_KEY = "6LdnnyYTAAAAALEyE9H_NJ4VUtTgXY2-O3YPe_4G";
    const WEB_SITE_URL = "https://ryanalin.famous-smoke.com/register";

    public static function getHash()
    {
        $api = new AntiCaptcha();
        $api->setVerboseMode(true);

        $api->setKey(self::KEY);

        $api->setWebsiteURL(self::WEB_SITE_URL);
        $api->setWebsiteKey(self::WEB_SITE_KEY);

        $api->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116");

        if (!$api->createTask()) {
            $api->debout("API v2 send failed - ".$api->getErrorMessage(), "red");
            return false;
        }

        $taskId = $api->getTaskId();

        if (!$api->waitForResult()) {
            $api->debout("could not solve captcha", "red");
            $api->debout($api->getErrorMessage());
        } else {
            $hash = $api->getTaskSolution();
            echo "\nhash result: ".$hash."\n\n";
            return $hash;
        }

        return false;
    }
}
