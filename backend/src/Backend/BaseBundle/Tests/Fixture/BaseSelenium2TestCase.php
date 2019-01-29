<?php
namespace Backend\BaseBundle\Tests\Fixture;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

abstract class BaseSelenium2TestCase extends \PHPUnit_Extensions_Selenium2TestCase
{
    protected $timeout = 500;

    /** @var  KernelInterface */
    protected $kernel;

    /** @var  ContainerInterface */
    protected $container;

    protected $baseUrl = 'http://127.0.0.1:8080';

    public static function browsers()
    {
        return array(
            array(
                'host' => '127.0.0.1',
                'port' => 4444,
                'browser' => 'firefox',
                'browserName' => 'firefox',
            ),
            array(
                'host' => '127.0.0.1',
                'port' => 4444,
                'browser' => 'chrome',
                'browserName' => 'chrome',
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();
        if(getenv('base_url') !== false){
            $this->baseUrl = getenv('base_url');
        }
        $this->bootKernel();
        $this->router = $this->container->get('router');
        $this->faker = $this->container->get('faker.generator');
        $this->setSeleniumServerRequestsTimeout($this->timeout);
        $this->setBrowserUrl($this->baseUrl);
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
        unset($this->kernel);
        unset($this->container);
        parent::tearDown();
    }

    public function setUpPage()
    {
        try {
            $this->adjectWidth();
        } catch (\Exception $e){
            return;
        }
    }

    protected function bootKernel()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
    }

    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function clickByXPath($xpath)
    {
        $this->moveto($this->byXPath($xpath));
        $this->click();
    }

    protected function clickByCssSelector($selector)
    {
        return $this->movetoAndClick($this->byCssSelector($selector));
    }

    protected function movetoAndClick($element)
    {
        if($this->getBrowser() == 'chrome'){
            $this->moveto($element);
        }
        return $element->click();
    }

    public function url($url = null)
    {
        if($url === null){
            return parent::url($url);
        }
        $result = parent::url($url);
        $this->waitUntil(function(){
            $states = $this->execute(array(
                'script' => "return document.readyState;",
                'args'   => array()
            ));
            if($states == 'complete'){
                return true;
            }
        }, 10000);
        return $result;
    }

    protected function waitUntilDisplayed($elementCssPath, $isDisplay = true, $timeout = 5000)
    {
        $this->waitUntil(function() use($elementCssPath, $isDisplay){
            if($this->byCssSelector($elementCssPath)->displayed() == $isDisplay){
                return true;
            }
        }, $timeout);
    }

    protected function adjectWidth()
    {
        $info = $this->getWindowSizeInfo();
        $this->currentWindow()->size($info);
        $img = new \imagick();
        $img->readImageBlob($this->currentScreenshot());
        $widthAdject = $info['width'] - $img->getimagewidth();
        $img->destroy();
        $info['width'] += $widthAdject;
        $this->currentWindow()->size($info);
    }

    protected function getWindowSizeInfo()
    {
        return array(
            'width' => 1280,
            'height' => 1024,
        );
    }
}