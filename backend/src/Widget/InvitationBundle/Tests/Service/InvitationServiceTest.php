<?php

namespace Widget\InvitationBundle\Tests\Service;

use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactory;
use Widget\InvitationBundle\Model\Invitation;
use Widget\InvitationBundle\Service\InvitationService;

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

    public function test_create()
    {
        //arrange
        $service = new InvitationService();
        $formFactory = $this->container->get("form.factory");
        $this->setObjectAttribute($service, 'formFactory', $formFactory);
        $parameter = array(
            "name" => '魏餅餅',
            "phone" => '0988777888',
            'email' => 'a@a.com'
        );

        //act
        $result = $service->create($parameter);

        //assert
        $this->assertInstanceOf(Invitation::class, $result);
    }

    public function test_create_壞參數()
    {
        //arrange
        $service = new InvitationService();
        $formFactory = $this->container->get("form.factory");
        $this->setObjectAttribute($service, 'formFactory', $formFactory);
        $parameter = array(
            "name" => '魏餅餅',
            "phone" => '0988777888',
            'number_of_people' => 0,
            'number_of_baby_seat' => -1,
            'email' => 'foobar',
        );

        //act
        $result = $service->create($parameter);

        //assert
        $this->assertInstanceOf(FormErrorIterator::class, $result);
    }
}
