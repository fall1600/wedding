<?php
namespace Backend\BaseBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\API\BaseController;
use Backend\BaseBundle\Event\TypeRolesEvent;
use Backend\BaseBundle\Model\OperationLogPeer;
use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Security\ApiUserProvider;
use Backend\BaseBundle\Security\SiteUserManager;
use Backend\BaseBundle\Security\SiteUserProvider;
use Backend\BaseBundle\Service\OperationLogger;
use Backend\BaseBundle\Token\SecretJWTToken;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends BaseController
{
    /**
     * @var OperationLogger
     * @DI\Inject("backend.base_bundle.operationlog")
     */
    protected $operationLogger;

    /**
     * @var ValidatorInterface
     * @DI\Inject()
     */
    protected $validator;

    /**
     * @var SiteUserManager
     * @DI\Inject()
     */
    protected $siteUserManager;

    /**
     * @var SiteUserProvider
     * @DI\Inject()
     */
    protected $siteUserProvider;

    /**
     * @var Serializer
     * @DI\Inject()
     */
    protected $serializer;

    /**
     * @var SecretJWTToken
     * @DI\Inject()
     */
    protected $secretJwtToken;

    /**
     * @var ApiUserProvider
     * @DI\Inject("backend_api_user_provider")
     */
    protected $apiUserProvider;

    /**
     * @var EventDispatcherInterface
     * @DI\Inject()
     */
    protected $eventDispatcher;

    /**
     * @Route("/login")
     * @Method({"PUT"})
     */
    public function loginAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $constraint = new Assert\Collection(array(
            'fields' => array(
                'username' => array(
                    new Assert\NotBlank(),
                ),
                'password' => array(
                    new Assert\NotBlank(),
                ),
            ),
        ));

        if(count($errors = $this->validator->validate($requestData, $constraint)) > 0){
            return $this->createValidatorErrorJsonResponse($errors);
        }

        if(!($user = $this->siteUserProvider->loadUserByUsernameOrEmail($requestData['username']))){
            return $this->createNotFoundJsonResponse();
        }

        if(!($this->siteUserManager->verifyUser($user, $requestData['password']))){
            return $this->createJsonResponse('', Response::HTTP_FORBIDDEN);
        }

        $user->setLastLogin(time())->save();
        $this->operationLogger->log($user, OperationLogPeer::MODIFY_TYPE_LOGIN, "loginname {$user->getLoginName()}");
        return $this->generateToken($request, $user);
    }

    protected function generateToken(Request $request, SiteUser $user)
    {
        $userData = $this->serializer->toArray(
            $user,
            SerializationContext::create()->setGroups(array('token'))
        );

        if (isset($userData['roles'])) {
            // 避免多權限 merge 時 token 內 role 從 array 變成 object 導致錯誤
            $userData['roles'] = array_values($userData['roles']);
        }
        return $this->createJsonResponse(array(
            'token' => $this->secretJwtToken->createJWTToken(
                $request,
                $userData
            ),
        ));
    }

    /**
     * @Route("/renewtoken")
     * @Method({"PUT"})
     */
    public function renewTokenAction(Request $request)
    {
        $user = $this->getUser();
        $user->reload(true);

        if(!$user->getEnabled()){
            return $this->createJsonResponse('', Response::HTTP_LOCKED);
        }

        return $this->generateToken($request, $user);
    }

    /**
     * @Route("/allroles")
     * @Method({"GET"})
     */
    public function allRolesAction(Request $request)
    {
        $typeRolesEvent = new TypeRolesEvent();
        $this->eventDispatcher->dispatch(TypeRolesEvent::EVENT_TYPE_ROLES, $typeRolesEvent);
        return $this->createJsonResponse($typeRolesEvent->getTypeRoles());
    }
}