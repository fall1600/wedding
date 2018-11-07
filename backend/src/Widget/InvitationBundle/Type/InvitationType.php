<?php

namespace Widget\InvitationBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Widget\InvitationBundle\Model\Invitation;

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
            ->add('number_of_people', IntegerType::class, array(
                'required' => false,
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
            ->add('number_of_vegetarian', IntegerType::class, array(
                'required' => false,
                'constraints' => array(
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
            ))
            ->add('number_of_baby_seat', IntegerType::class, array(
                'required' => false,
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
