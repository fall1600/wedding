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
                        'message' => 'error.invitation.required',
                    )),
                ),
            ))
            ->add('')
        ;
    }
}
