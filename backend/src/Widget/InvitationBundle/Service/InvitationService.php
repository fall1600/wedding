<?php

namespace Widget\InvitationBundle\Service;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Validator\Constraints as Assert;
use Widget\InvitationBundle\Model\Invitation;

/**
 * @DI\Service("invitation_service")
 */
class InvitationService
{
    use FormBinder;

    /**
     * @param $parameter
     * @param \PropelPDO|null $conn
     * @return FormErrorIterator|Invitation
     */
    public function create($parameter, \PropelPDO $conn = null)
    {
        $form = $this->bindObjectWithRawData($invitation = new Invitation(), $parameter);
        if (!$form->isValid()) {
            return $form->getErrors(true, false);
        }
        $invitation->save($conn);
        return $invitation;
    }

    /**
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array(
            (new APIFormTypeItem('name'))
                ->setOptions(array(
                    'constraints' => array(
                        new Assert\NotBlank(array(
                            'message' => 'error.invitation.name.required',
                        )),
                    ))
                ),
            (new APIFormTypeItem('nickname')),
            (new APIFormTypeItem('phone'))
                ->setOptions(array(
                    'constraints' => array(
                        new Assert\NotBlank(array(
                            'message' => 'error.invitation.phone.required'
                        ))
                    )
                )),
            (new APIFormTypeItem('number_of_people'))
                ->setOptions(array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\GreaterThanOrEqual(array(
                            'value' => 1,
                            'message' => 'error.invitation.number_of_people.wrong'
                        ))
                    )
                )),
            (new APIFormTypeItem('number_of_vegetarian'))
                ->setOptions(array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\GreaterThanOrEqual(array(
                            'value' => 0,
                            'message' => 'error.invitation.number_of_vegetarian.wrong'
                        ))
                    )
                )),
            (new APIFormTypeItem('number_of_baby_seat'))
                ->setOptions(array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\GreaterThanOrEqual(array(
                            'value' => 0,
                            'message' => 'error.invitation.number_of_baby_seat.wrong'
                        ))
                    )
                )),
            (new APIFormTypeItem('address')),
            (new APIFormTypeItem('email'))
                ->setOptions(array(
                    'constraints' => array(
                        new Assert\Email(array(
                            'strict' => true,
                            'message' => 'error.invitation.email.wrong'
                        ))
                    )
                )),
            (new APIFormTypeItem('attend')),
            (new APIFormTypeItem('known_from')),
            (new APIFormTypeItem('note')),
        );
    }
}
