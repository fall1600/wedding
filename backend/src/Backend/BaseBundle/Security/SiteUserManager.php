<?php
namespace Backend\BaseBundle\Security;

use Backend\BaseBundle\Model;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\Request;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @Service("site_user_manager")
 */
class SiteUserManager
{

    /** @var  GuardAuthenticatorHandler */
    protected $authenticatorHandler;

    /** @var  \Symfony\Component\Security\Core\Encoder\EncoderFactory */
    protected $encoderFactory;

    /**
     * @InjectParams({
     *     "authenticatorHandler" = @Inject("security.authentication.guard_handler"),
     *     "encoderFactory" = @Inject("security.encoder_factory")
     * })
     */
    public function injectService($authenticatorHandler, $encoderFactory)
    {
        $this->authenticatorHandler = $authenticatorHandler;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param $firewall
     * @param Model\SiteUser $user
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    protected function createToken($firewall, Model\SiteUser $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }

    protected function getEncoder(UserInterface $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    public function loginUser(Model\SiteUser $user, Request $request)
    {
        $user->setLastLogin(time());
        $user->save();
        $token = $this->createToken('site_backend', $user);
        $this->authenticatorHandler->authenticateWithToken($token, $request);
    }

    public function updateUser(Model\SiteUser $user)
    {
        if($user->getPlainPassword() !== null){
            $user->regenerateSalt();
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
        }
        $user->save();
    }

    public function verifyUser(Model\SiteUser $user, $password)
    {
        if(!$user->getEnabled()){
            return false;
        }
        $encoder = $this->getEncoder($user);
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }
}