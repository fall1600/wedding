<?php
namespace Backend\BaseBundle\Tests\Fixture;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class BaseKernelTestCase extends BaseTestCase
{

    /**
     * @var \Symfony\Component\HttpKernel\Kernel;
     */
    protected $kernel;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->bootKernel();
    }

    protected function tearDown()
    {
        $this->kernel->shutdown();
        unset($this->kernel);
        unset($this->container);
        parent::tearDown();
    }

    protected function bootKernel()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
    }

}