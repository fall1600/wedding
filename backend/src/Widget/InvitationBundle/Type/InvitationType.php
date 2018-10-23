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
            ->add('number_of_people')
            ->add('address')
            ->add('email')
            ->add('attend')
            ->add('known_from')
            ->add('is_vegetarian')
            ->add('baby_seat')
            ->add('note')
        ;
    }
}
