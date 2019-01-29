<?php
namespace Backend\BaseBundle\Security;


use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Token\SecretJWTToken;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @DI\Service("backend_api_user_provider")
 */
class ApiUserProvider implements UserProviderInterface
{
    /** @var  SecretJWTToken */
    protected $secretJWTToken;

    /** @var  PropertyAccessor */
    protected $propertyAccessor;

    protected function loadUserFromArray($array)
    {
        $user = new SiteUser();
        foreach ($array as $path => $value) {
            $this->propertyAccessor->setValue($user, $path, $value);
        }
        $user->setNew(false);
        return $user;
    }

    /**
     * @InjectParams()
     */
    public function injectPropertyAccessor(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @InjectParams({
     *    "secretJWTToken" = @Inject("secret_jwt_token")
     * })
     */
    public function injectSecretJWTToken(SecretJWTToken $secretJWTToken)
    {
        $this->secretJWTToken = $secretJWTToken;
    }

    /**
     * @param $jwtToken
     * @param $issuer
     * @param $audience
     * @return SiteUser|null
     */
    public function loadUserByJWTToken($jwtToken, $issuer, $audience)
    {
        $data = $this->secretJWTToken->verify($jwtToken, $issuer, $audience);

        if($data === null){
            return null;
        }

        return $this->loadUserFromArray($data);
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException();
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return SiteUser::class === $class;
    }
}