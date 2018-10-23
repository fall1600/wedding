<?php

namespace Widget\InvitationBundle\FormConfig;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;

class Invitation
{
    /**
     * @return APIFormTypeItem[]
     */
    public function getFormConfig()
    {
        return array(
            (new APIFormTypeItem('name')->setOptions(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'error.invitation.name.required',
                    )),
                ),
            )),
        );
    }
}