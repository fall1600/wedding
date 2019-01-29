<?php
namespace Backend\BaseBundle\Security;


use Backend\BaseBundle\Token\SecretJWTToken;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

/**
 * @DI\Service("backend_api_authenticator", public=false)
 */
class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /** @var  SecretJWTToken */
    protected $secretJWTToken;
    /** @var  ApiUserProvider */
    protected $apiUserProvider;

    /**
     * @InjectParams({
     *     "apiUserProvider" = @Inject("backend_api_user_provider")
     * })
     */
    public function injectApiUserProvider(ApiUserProvider $apiUserProvider)
    {
        $this->apiUserProvider = $apiUserProvider;
    }

    public function createToken(Request $request, $providerKey)
    {
        $auth = $request->headers->get('Authorization');

        if($auth == null){
            throw new BadCredentialsException();
        }

        if(!(preg_match('/^Bearer\s+(.*)$/i', $auth, $match))){
            throw new BadCredentialsException();
        }

        if(!($user = $this->apiUserProvider->loadUserByJWTToken(
            $match[1],
            $request->getHttpHost(),
            $request->headers->get('Origin')
        ))){
            throw new BadCredentialsException();
        }
        return new PreAuthenticatedToken($user, null, $providerKey, $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        return $token;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            array(
                'message' => $exception->getMessageKey()
            ),
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }
}