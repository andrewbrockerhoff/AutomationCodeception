<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Send Report to PractiTest
     */
    function sendResultToPractiTest() {
        $steps = [];
        $scenarioSteps = $this->getScenario()->getSteps();
        foreach ($scenarioSteps as $step) {
            $status = $step->hasFailed() ? 'FAILED' : 'PASSED';
            $args = $step->getArguments();
            if (in_array("practitest_fix", $args)) {
                $status = 'PASSED';
            }
            $steps[] = [
                'name' => substr(strip_tags($step->toString(1000)), 0, 255),
                'expected-results' => '',
                'status' => $status,
                'description' => $step->toString(1000),
            ];
        }
        // Get Instance Id
        $project_id = \Helper\Practitest::PROJECT_ID;
        $credentials = base64_encode(\Helper\Practitest::USERNAME.':'.\Helper\Practitest::TOKEN);
        $client = new \GuzzleHttp\Client();

        $response = $client
            ->get(
                'https://api.practitest.com/api/v2/projects/'.$project_id.'/instances.json?name_like=' . $this->getScenario()->current('name'),
                [
                    'headers' => [
                        'Authorization' => 'Basic ' . $credentials,
                        'content-type' => 'application/json'
                    ],
                ])
            ->getBody()
            ->getContents();

        if (isset(json_decode($response)->data[0])) {
            $instance_id = json_decode($response)->data[0]->id;
            // Save result
            $response = $client
                ->post(
                    'https://api.practitest.com/api/v2/projects/' . $project_id . '/runs.json',
                    [
                        'body' =>
                            '{"data": {"type": "instances", "attributes": {"instance-id": '.$instance_id.'}, "steps": {"data": '.json_encode($steps).' }}} ',
                        'headers' => [
                            'Authorization' => 'Basic ' . $credentials,
                            'content-type' => 'application/json'
                        ],
                    ])
                ->getBody()
                ->getContents();
        }
    }

    /**
     * Method is using to fix practitest extra failings
     *
     * @param $element
     * @param $practitest_fix
     * @return mixed|null
     */
    public function waitAndClickWrap($element, $practitest_fix = false) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('waitAndClick', func_get_args()));
    }

    /**
     * Method is using to fix practitest extra failings
     *
     * @param $text
     * @param null $timeout
     * @param null $selector
     * @param $practitest_fix
     * @return mixed|null
     */
    public function waitForTextWrap($text, $timeout = null, $selector = null, $practitest_fix = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('waitForText', func_get_args()));
    }

    /**
     * Method is using to fix practitest extra failings.
     *
     * @param $element
     * @param null $timeout
     * @param $practitest_fix
     * @return mixed|null
     */
    public function waitForElementVisibleWrap($element, $timeout = null, $practitest_fix = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('waitForElementVisible', func_get_args()));
    }

    /**
     * Method is using to fix practitest extra failings
     *
     * @param $element
     * @param null $timeout
     * @param $practitest_fix
     * @return mixed|null
     */
    public function waitForElementNotVisibleWrap($element, $timeout = null, $practitest_fix = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('waitForElementNotVisible', func_get_args()));
    }

    /**
     * Method is using to fix practitest extra failings
     *
     * @param $element
     * @param null $timeout
     * @param $practitest_fix
     * @return mixed|null
     */
    public function waitForElementWrap($element, $timeout = null, $practitest_fix = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Action('waitForElement', func_get_args()));
    }

    /**
     * Method is using to fix practitest extra failings
     *
     * @param $text
     * @param null $selector
     * @param $practitest_fix
     * @return mixed|null
     */
    public function seeWrap($text, $selector = null, $practitest_fix = null) {
        return $this->getScenario()->runStep(new \Codeception\Step\Assertion('see', func_get_args()));
    }
}
