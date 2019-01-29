<?php
namespace Backend\BaseBundle\Tests\Controller\API;


use Backend\BaseBundle\Model\SiteConfig;
use Backend\BaseBundle\Model\SiteConfigQuery;
use Backend\BaseBundle\Model\SiteQuery;
use Backend\BaseBundle\Tests\Fixture\BackendWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class CORSControllerTest extends BackendWebTestCase
{

    public function test_optionsAction_multiple_path_level()
    {
        //arrange
        $url = '/api/foo/bar/baz';

        //act
        $this->client->request('OPTIONS', $url);
        $response = $this->client->getResponse();

        //assert
        $this->assertTrue($response->isOk());
    }

    public function test_optionsAction_multiple_path_level_with_dot()
    {
        //arrange
        $url = '/api/f.o.o.../bar/baz';

        //act
        $this->client->request('OPTIONS', $url);
        $response = $this->client->getResponse();

        //assert
        $this->assertTrue($response->isOk());
    }

    public function test_optionsAction_single_path_level()
    {
        //arrange
        $url = '/api/';

        //act
        $this->client->request('OPTIONS', $url);
        $response = $this->client->getResponse();

        //assert
        $this->assertTrue($response->isOk());
    }

    public function test_optionsAction_with_cors()
    {
        //arrange
        $origin = 'http://localhost';
        $url = '/api/foo/bar/baz';

        //act
        $this->client->request('OPTIONS', $url, array(), array(), array(
            'HTTP_ORIGIN' => $origin,
        ));
        $response = $this->client->getResponse();

        //assert
        $this->assertTrue($response->isOk());
        $this->assertEquals($origin, $response->headers->get('access-control-allow-origin'));
    }

    public function test_optionsAction_with_cors_bad_origin()
    {
        //arrange
        $origin = 'http://example.com';
        $url = '/api/foo/bar/baz';

        //act
        $this->client->request('OPTIONS', $url, array(), array(), array(
            'HTTP_ORIGIN' => $origin,
        ));
        $response = $this->client->getResponse();

        //assert
        $this->assertFalse($response->isOk());
    }
}