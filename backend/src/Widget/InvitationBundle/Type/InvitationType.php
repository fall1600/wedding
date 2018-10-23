<?php

namespace Widget\InvitationBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'error.invitation.name.required',
                    )),
                ),
            ))
            ->add('nickname')
            ->add('phone', null, array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'error.invitation.phone.required'
                    ))
                )
            ))
            ->add('number_of_people', null, array(
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array(
                        'value' => 1,
                        'message' => 'error.invitation.number_of_people.wrong'
                    ))
                )
            ))
            ->add('address')
            ->add('email', null, array(
                'constraints' => array(
                    new Assert\Email(array(
                        'strict' => true,
                        'message' => 'error.invitation.email.wrong'
                    ))
                )
            ))
            ->add('attend')
            ->add('known_from')
            ->add('is_vegetarian')
            ->add('baby_seat', null, array(
                'constraints' => array(
                    new Assert\GreaterThanOrEqual(array(
                        'value' => 0,
                        'message' => 'error.invitation.baby_seat.wrong'
                    ))
                )
            ))
            ->add('note')
        ;
    }
}
