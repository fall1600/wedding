<?php

namespace Widget\InvitationBundle\Controller\API;

use Backend\BaseBundle\Controller\API\BaseController;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Widget\InvitationBundle\Model\InvitationQuery;
use Widget\InvitationBundle\Service\InvitationService;

/**
 * @Route("/invitation")
 */
class InvitationController extends BaseController
{
    /**
     * @var InvitationService
     * @DI\Inject()
     */
    protected $invitationService;

    /**
     * @Route("s")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $parameter = json_decode($request->getContent(), true);
        $result = $this->invitationService->create($parameter);
        return $this->createJsonSerializeResponse($result, array("detail"));
    }

    /**
     * @Route("s")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $result = InvitationQuery::create()->find();
        return $this->createJsonSerializeResponse($result, array("list"));
    }
}
