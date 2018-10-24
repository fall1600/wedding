<?php

namespace Widget\InvitationBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\BackendAPI\BaseBackendAPIController;
use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Widget\InvitationBundle\Model\Invitation;
use Widget\InvitationBundle\Model\InvitationPeer;
use Widget\InvitationBundle\Model\InvitationQuery;

/**
 * @Route("/invitation")
 */
class InvitationController extends BaseBackendAPIController
{
    /**
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array();
    }

    /**
     * @Route("s")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_INVITATION_READ')")
     */
    public function searchAction(Request $request)
    {
        return $this->doSearch($request->query->all(), InvitationQuery::create()->distinct(), InvitationPeer::class);
    }

    /**
     * @Route("/{id}")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_INVITATION_READ')")
     */
    public function readAction(Invitation $invitation)
    {
        return $this->createJsonSerializeResponse($invitation, array("detail"));
    }

    /**
     * @Route("s")
     * @Method({"POST"})
     * @Security("has_role_or_superadmin('ROLE_INVITATION_WRITE')")
     */
    public function createAction(Request $request)
    {
        $invitation = new Invitation();
        $form = $this->bindObject($invitation, $request->getContent());
        if(!($form->isValid())) {
            return $this->createFormErrorJsonResponse($form->getErrors(true));
        }
        return $this->doProcessForm($invitation, $request->getContent());
    }

    /**
     * @Route("/{id}")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_INVITATION_WRITE')")
     */
    public function updateAction(Request $request, Invitation $invitation)
    {
        $form = $this->bindObject($invitation, $request->getContent());
        if(!($form->isValid())) {
            return $this->createFormErrorJsonResponse($form->getErrors(true));
        }
        return $this->doProcessForm($invitation, $request->getContent());
    }

    /**
     * @Route("/{id}")
     * @Method({"DELETE"})
     * @Security("has_role_or_superadmin('ROLE_INVITATION_WRITE')")
     */
    public function deleteAction(Invitation $invitation)
    {
        $this->deleteObject($invitation);
        return $this->createJsonResponse();
    }
}
