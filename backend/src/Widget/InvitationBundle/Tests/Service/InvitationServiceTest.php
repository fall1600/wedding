<?php

namespace Widget\InvitationBundle\Tests\Service;

use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;
use Symfony\Component\Form\FormFactory;

class InvitationServiceTest extends BaseKernelTestCase
{
    public function test_inject()
    {
        //arrange

        //act
        $service = $this->container->get('invitation_service');

        //assert
        $this->assertInstanceOf(FormFactory::class, $this->getObjectAttribute($service, 'formFactory'));
    }
}
