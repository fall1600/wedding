<?php

namespace Widget\InvitationBundle\Controller\API;

use Backend\BaseBundle\Controller\API\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/invitation")
 */
class InvitationController extends BaseController
{
    /**
     * @Route("s")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {

    }
}
