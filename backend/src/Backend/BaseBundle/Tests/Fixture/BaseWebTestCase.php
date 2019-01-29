<?php
namespace Backend\BaseBundle\Tests\Fixture;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\BrowserKit\Cookie;

class BaseWebTestCase extends WebTestCase
{
    /** @var \Symfony\Bundle\FrameworkBundle\Client */
    protected $client;

    /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;

    /** @var  \Symfony\Component\HttpFoundation\Session\Session */
    protected $session;

    /** @var  \Faker\Generator */
    protected $faker;

    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->router = $this->client->getContainer()->get('router');
        $this->session = $this->client->getContainer()->get('session');
        $this->faker = $this->client->getContainer()->get('faker.generator');
        \Propel::disableInstancePooling();
    }

    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    protected function tearDown()
    {
        $this->client = null;
        parent::tearDown();
    }

    protected function writeServerSession()
    {
        $this->session->save();
        $cookie = new Cookie($this->session->getName(), $this->session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function callObjectMethod($object, $methodName)
    {
        $args = func_get_args();
        array_shift($args); //$object
        array_shift($args); //$methodName
        $reflect = new \ReflectionClass($object);
        $method = $reflect->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    protected function setObjectAttribute($object, $attributeName, $value)
    {
        $reflect = new \ReflectionClass($object);
        $property = $reflect->getProperty($attributeName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}