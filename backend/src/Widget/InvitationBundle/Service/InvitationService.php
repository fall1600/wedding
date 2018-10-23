<?php

namespace Widget\InvitationBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormFactory;
use Widget\InvitationBundle\Model\Invitation;
use Widget\InvitationBundle\Type\InvitationType;

/**
 * @DI\Service("invitation_service")
 */
class InvitationService
{
    /** @var FormFactory */
    protected $formFactory;

    /**
     * @DI\InjectParams({
     *   "formFactory" = @DI\Inject("form.factory")
     * })
     */
    public function injectFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create($parameter)
    {
        $form = $this->formFactory->create(InvitationType::class, $invitation = new Invitation(), array('csrf_protection' => false));
        $form->submit($parameter);
        if (!$form->isValid()) {
            throw new \Exception();
        }
        return $invitation;
    }
}
