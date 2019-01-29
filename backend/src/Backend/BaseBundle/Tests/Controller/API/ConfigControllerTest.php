<?php
namespace Backend\BaseBundle\Tests\Controller\API;


use Backend\BaseBundle\Model\SiteConfig;
use Backend\BaseBundle\Model\SiteConfigQuery;
use Backend\BaseBundle\Model\SiteQuery;
use Backend\BaseBundle\SiteConfig\ModelConfig;
use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use Backend\BaseBundle\Tests\Fixture\BackendWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class ConfigControllerTest extends BackendWebTestCase
{

    public function test_indexAction_not_found()
    {
        //arrange
        $config = 'bad_config';

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('backend_base_api_config_index', array('config' => $config))
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isNotFound());
    }

    public function test_indexAction_google_tag_manager()
    {
        //arrange
        $config = 'google_tag_manager';
        $configContent = 'test_content';

        $modelConfig = $this->getMockBuilder(ModelConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $modelConfig
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(function($config) use($configContent){
                $this->assertEquals('system', $config);
                return array(
                    'google_tag_manager' => $configContent,
                );
            });

        $mockConfigBuilder = $this->getMockBuilder(SiteConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock()
            ;
        $mockConfigBuilder
            ->expects($this->atLeastOnce())
            ->method('build')
            ->willReturn($modelConfig);
        $this->client->getContainer()->set('backend_base.site_config_builder', $mockConfigBuilder);

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('backend_base_api_config_index', array('config' => $config))
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertEquals($result['config'], $configContent);
    }

    public function test_indexAction_google_tag_manager_null()
    {
        //arrange
        $config = 'google_tag_manager';
        $configContent = 'test_content';

        $modelConfig = $this->getMockBuilder(ModelConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $modelConfig
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(function($config) use($configContent){
                $this->assertEquals('system', $config);
                return array();
            });

        $mockConfigBuilder = $this->getMockBuilder(SiteConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock()
        ;
        $mockConfigBuilder
            ->expects($this->atLeastOnce())
            ->method('build')
            ->willReturn($modelConfig);
        $this->client->getContainer()->set('backend_base.site_config_builder', $mockConfigBuilder);

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('backend_base_api_config_index', array('config' => $config))
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertNull($result['config']);
    }

    public function test_indexAction_recaptcha_site_key()
    {
        //arrange
        $config = 'recaptcha_site_key';
        $configContent = 'test_content';

        $modelConfig = $this->getMockBuilder(ModelConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $modelConfig
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(function($config) use($configContent){
                $this->assertEquals('system', $config);
                return array(
                    'recaptcha_site_key' => $configContent,
                );
            });

        $mockConfigBuilder = $this->getMockBuilder(SiteConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock()
        ;
        $mockConfigBuilder
            ->expects($this->atLeastOnce())
            ->method('build')
            ->willReturn($modelConfig);
        $this->client->getContainer()->set('backend_base.site_config_builder', $mockConfigBuilder);

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('backend_base_api_config_index', array('config' => $config))
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertEquals($result['config'], $configContent);
    }

    public function test_indexAction_recaptcha_site_key_null()
    {
        //arrange
        $config = 'recaptcha_site_key';
        $configContent = 'test_content';

        $modelConfig = $this->getMockBuilder(ModelConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $modelConfig
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnCallback(function($config) use($configContent){
                $this->assertEquals('system', $config);
                return array();
            });

        $mockConfigBuilder = $this->getMockBuilder(SiteConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('build'))
            ->getMock()
        ;
        $mockConfigBuilder
            ->expects($this->atLeastOnce())
            ->method('build')
            ->willReturn($modelConfig);
        $this->client->getContainer()->set('backend_base.site_config_builder', $mockConfigBuilder);

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('backend_base_api_config_index', array('config' => $config))
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertNull($result['config']);
    }

}