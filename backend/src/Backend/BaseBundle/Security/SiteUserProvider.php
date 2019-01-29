<?php
namespace Backend\BaseBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Backend\BaseBundle\Model;

/**
 * @Service("site_user_provider")
 */
class SiteUserProvider implements UserProviderInterface
{
    public function loadUserByEmail($email)
    {
        return Model\SiteUserQuery::create()
            ->filterByEmail($email)
            ->findOne();
    }

    public function loadUserByUsername($username)
    {
        return $this->loadUserByLoginName($username);
    }

    public function loadUserByLoginName($username)
    {
        return Model\SiteUserQuery::create()
            ->filterByLoginName($username)
            ->findOne();
    }

    public function loadUserByUsernameOrEmail($usernameOrEmail)
    {
        if($siteUser = $this->loadUserByUsername($usernameOrEmail)){
            return $siteUser;
        }
        return $this->loadUserByEmail($usernameOrEmail);
    }

    public function refreshUser(UserInterface $user)
    {
        if($user instanceof Model\SiteUser){
            $user->reload(true);
            return $user;
        }
        throw new \InvalidArgumentException("user must be an instance of ".Model\SiteUser::class);
    }

    public function supportsClass($class)
    {
        return $class == Model\SiteUser::class;
    }

}