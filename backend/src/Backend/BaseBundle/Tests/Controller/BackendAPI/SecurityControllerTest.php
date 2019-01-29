<?php
namespace Backend\BaseBundle\Tests\Controller\BackendAPI;


use Backend\BaseBundle\Model\OperationLog;
use Backend\BaseBundle\Model\OperationLogQuery;
use Backend\BaseBundle\Model\SiteConfig;
use Backend\BaseBundle\Model\SiteConfigQuery;
use Backend\BaseBundle\Model\SiteQuery;
use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Model\SiteUserQuery;
use Backend\BaseBundle\SiteConfig\ModelConfig;
use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use Backend\BaseBundle\Tests\Fixture\BackendWebTestCase;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class SecurityControllerTest extends BackendWebTestCase
{
    public function test_loginAction_emptyField()
    {
        //arrange
        $data = array();

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_login'),
            array(),
            array(),
            array(),
            json_encode($data)
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
    }

    public function test_loginAction_user_not_found()
    {
        //arrange
        $username = 'bad_admin';
        $password = '12345';
        $data = array(
            'username' => $username,
            'password' => $password,
        );

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_login'),
            array(),
            array(),
            array(),
            json_encode($data)
        );
        $response = $this->client->getResponse();

        //assert
        $this->assertTrue($response->isNotFound());
    }

    public function test_loginAction_user_bad_password()
    {
        //arrange
        $username = 'admin';
        $password = '12345';

        $userProvider = $this->client->getContainer()->get('site_user_provider');
        $userManager =  $this->client->getContainer()->get('site_user_manager');
        $user = $userProvider->loadUserByUsernameOrEmail($username);
        $user->setPlainPassword('bad_password');
        $userManager->updateUser($user);

        $data = array(
            'username' => $username,
            'password' => $password,
        );

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_login'),
            array(),
            array(),
            array(),
            json_encode($data)
        );
        $response = $this->client->getResponse();

        //assert
        $this->assertTrue($response->isForbidden());
    }

    public function test_loginAction()
    {
        //arrange
        $username = 'admin';
        $password = '12345';

        $userProvider = $this->client->getContainer()->get('site_user_provider');
        $userManager =  $this->client->getContainer()->get('site_user_manager');
        $user = $userProvider->loadUserByUsernameOrEmail($username);
        $user->setPlainPassword($password);
        $userManager->updateUser($user);
        $originLoginTime = $user->getLastLogin('U');
        OperationLogQuery::create()->deleteAll();

        $data = array(
            'username' => $username,
            'password' => $password,
        );

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_login'),
            array(),
            array(),
            array(),
            json_encode($data)
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $user->reload(true);
        $operationLog = OperationLogQuery::create()->findOne();

        //assert
        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('token', $result);
        $this->assertNotEquals($originLoginTime, $user->getLastLogin('U'));
        $this->assertNotEmpty($operationLog);
    }

    public function test_renewTokenAction_no_authorization()
    {
        //arrange
        $token = '';

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_renewtoken')
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals('Invalid credentials.', $result['message']);
    }

    public function test_renewTokenAction_bad_user()
    {
        //arrange
        $loginName = 'baduser';
        $origin = 'http://localhost';
        $user = SiteUserQuery::create()->findOneByLoginName($loginName);
        $token = $this->createToken($user, $origin);

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_renewtoken'),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertEquals(Response::HTTP_LOCKED, $response->getStatusCode());
    }

    public function test_renewTokenAction_ok()
    {
        //arrange
        $loginName = 'admin';
        $origin = 'http://localhost';
        $user = SiteUserQuery::create()->findOneByLoginName($loginName);
        $token = $this->createToken($user, $origin);
        sleep(1);

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('backend_base_backendapi_security_renewtoken'),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('token', $result);
        $this->assertNotEquals($token, $result['token']);
    }

}