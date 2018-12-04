<?php

namespace Widget\InvitationBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\BackendAPI\BaseBackendAPIController;
use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Form\Type\RelationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Widget\CategoryBundle\Model\Category;
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
        return array(
            (new APIFormTypeItem('name'))
                ->setOptions(array(
                    "constraints" => array(
                        new NotBlank(array(
                            "message" => 'error.invitation.name.required',
                        ))
                    )
                )),
            new APIFormTypeItem('nickname'),
            (new APIFormTypeItem('phone'))
                ->setOptions(array(
                    "constraints" => array(
                        new NotBlank(array(
                            "message" => 'error.invitation.phone.required',
                        ))
                    )
                )),
            (new APIFormTypeItem('number_of_people'))
                ->setOptions(array(
                    "constraints" => array(
                        new GreaterThanOrEqual(array(
                            "value" => 0,
                            "message" => 'error.invitation.number_of_people.wrong',
                        ))
                    )
                )),
            (new APIFormTypeItem('number_of_vegetarian'))
                ->setOptions(array(
                    "constraints" => array(
                        new GreaterThanOrEqual(array(
                            "value" => 0,
                            "message" => 'error.invitation.number_of_vegetarian.wrong',
                        )),
                        new Callback(function($value, ExecutionContextInterface $context) {
                            /** @var Invitation $object */
                            $object = $context->getRoot()->getData();
                            if ($value > $object->getNumberOfPeople()) {
                                $context->addViolation('error.invitation.number_of_vegetarian.greater_than.number_of_people');
                            }
                        }),
                    )
                )),
            (new APIFormTypeItem('number_of_baby_seat'))
                ->setOptions(array(
                    "constraints" => array(
                        new GreaterThanOrEqual(array(
                            "value" => 0,
                            "message" => 'error.invitation.number_of_baby_seat.wrong',
                        )),
                    )
                )),
            new APIFormTypeItem('address'),
            (new APIFormTypeItem('email'))
                ->setOptions(array(
                    "constraints" => array(
                        new Email(array(
                            "message" => 'error.invitation.email.wrong',
                        ))
                    )
                )),
            (new APIFormTypeItem('attend'))
                ->setOptions(array(
                    "constraints" => array(
                        new NotBlank(array(
                            "message" => 'error.invitation.attend.required',
                        )),
                        new Callback(function($value, ExecutionContextInterface $context) {
                            if (!in_array($value, InvitationPeer::getValueSets()[InvitationPeer::ATTEND])) {
                                $context->addViolation('error.invitation.attend.wrong');
                            }
                        })
                    )
                )),
            (new APIFormTypeItem('known_from'))
                ->setOptions(array(
                    "constraints" => array(
                        new NotBlank(array(
                            "message" => 'error.invitation.known_from.required',
                        )),
                        new Callback(function($value, ExecutionContextInterface $context) {
                            if (!in_array($value, InvitationPeer::getValueSets()[InvitationPeer::KNOWN_FROM])) {
                                $context->addViolation('error.invitation.known_from.wrong');
                            }
                        })
                    )
                )),
            new APIFormTypeItem('note'),
            (new APIFormTypeItem('categories'))
                ->setFieldType(RelationType::class)
                ->setOptions(array(
                    'class' => Category::class,
                    'multiple' => true
                ))
        );
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
