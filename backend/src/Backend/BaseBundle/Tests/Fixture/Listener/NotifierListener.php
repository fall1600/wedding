<?php
namespace Backend\BaseBundle\Tests\Fixture\Listener;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use Joli\JoliNotif\Util\OsHelper;
use PHPUnit_Framework_Test as Test;
use PHPUnit_Framework_TestSuite as TestSuite;
use PHPUnit_Framework_AssertionFailedError as AssertionFailedError;

class NotifierListener extends \PHPUnit_Framework_BaseTestListener
{
    private $notifier;
    private $errors = 0;
    private $failures = 0;
    private $tests = 0;
    private $suites = 0;
    private $ended_suites = 0;

    public function __construct()
    {
        $this->notifier = NotifierFactory::create();
    }

    public function addError(Test $test, \Exception $e, $time)
    {
        $this->errors++;
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->failures++;
    }

    public function startTestSuite(TestSuite $suite)
    {
        $this->suites++;
    }

    public function endTestSuite(TestSuite $suite)
    {
        $this->ended_suites++;

        if ($this->suites > $this->ended_suites) {
            return;
        }

        $failures = $this->errors + $this->failures;
        if ($failures === 0) {
            $title = 'Success';
            $body  = sprintf('%d/%d tests passed', $this->tests, $this->tests);
            $icon = realpath(__DIR__.'/../Resource/icon-success.png');
        } else {
            $title = 'Failed';
            $body  = sprintf('%d/%d tests failed', $failures, $this->tests);
            $icon = realpath(__DIR__.'/../Resource/icon-fail.png');
        }

        $notification = (new Notification())
            ->setTitle($title)
            ->setBody($body)
            ->setIcon($icon)
        ;
        $this->notifier->send($notification);
    }

    public function startTest(Test $test)
    {
        $this->tests++;
    }
}
