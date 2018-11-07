<?php

namespace Widget\InvitationBundle\Controller\API;

use Backend\BaseBundle\Controller\BackendAPI\BaseBackendAPIController;
use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Widget\InvitationBundle\Model\Invitation;
use Widget\InvitationBundle\Type\InvitationType;

/**
 * @Route("/invitation")
 */
class InvitationController extends BaseBackendAPIController
{
    /**
     * @Route("s")
     * @Method({"POST"})
     */
    public function create(Request $request)
    {
        $params = json_decode($request->getContent(), true);
        $form = $this->createForm(
            InvitationType::class,
            $invitation = new Invitation(),
            array('csrf_protection' => false)
        );
        $form->submit($params);
        if (!$form->isValid()) {
            return $this->createJsonSerializeResponse(
                $form->getErrors(true),
                array(),
                Response::HTTP_BAD_REQUEST
            );
        }
        $invitation->save();
        return $this->createJsonSerializeResponse($invitation, array("detail"));
    }

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
                            "value" => 1,
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
            new APIFormTypeItem('attend'),
            new APIFormTypeItem('known_from'),
            new APIFormTypeItem('note'),
        );
    }
}
