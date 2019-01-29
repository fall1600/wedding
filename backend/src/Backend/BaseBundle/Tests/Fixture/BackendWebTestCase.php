<?php
namespace Backend\BaseBundle\Tests\Fixture;
use Backend\BaseBundle\Model;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BackendWebTestCase extends BaseWebTestCase
{
    private $defaultRoles = null;

    /** @var  Model\SiteUser */
    private $user;

    protected function tearDown()
    {
        if($this->user) {
            $this->user->setDefaultRoles($this->defaultRoles);
            $this->user->save();
            $this->user = null;
            $this->defaultRoles = null;
        }
        parent::tearDown();
    }

    protected function loginWithRoles(Model\SiteUser $user, $roles = array())
    {
        $firewall = 'site_backend';
        $this->defaultRoles = $user->getDefaultRoles();
        $this->user = $user;
        $user->setDefaultRoles($roles);
        $user->keepUpdateDateUnchanged();
        $user->save();
        $token = new UsernamePasswordToken($user, null, $firewall, $roles);
        $this->session->set('_security_'.$firewall, serialize($token));
        $this->writeServerSession();
    }

    protected function createToken($user, $origin = 'http://localhost')
    {
        $request = Request::create('');
        $request->headers->set('Origin', $origin);
        $secretJwtToken = $this->client->getContainer()->get('secret_jwt_token');
        $serializer = $this->client->getContainer()->get('serializer');
        return $secretJwtToken->createJWTToken($request, $serializer->toArray(
            $user,
            SerializationContext::create()->setGroups(array('token'))
        ));
    }

}