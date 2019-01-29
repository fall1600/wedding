<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/9/13
 * Time: 下午2:45
 */
namespace Backend\BaseBundle\Tests\Service\Mail;

use Backend\BaseBundle\Message\GeneralMailMessage;
use Backend\BaseBundle\Service\Mail\MailTemplateMessageBuilder;
use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;

class MailTemplateMessageBuilderTest extends BaseKernelTestCase
{
    public function test_injects()
    {
        //arrange
        //act
        $mailTemplateMessage = $this->container->get('mail_template_message_builder');
        //assert
        $this->assertInstanceOf(SiteConfigBuilder::class, $this->getObjectAttribute($mailTemplateMessage, 'siteConfigBuilder'));
    }

    public function test_create()
    {
        //arrange
        $email = 'bubble@dgfactor.com';
        $template = 'register';
        $parameter = array();

        $mailTemplateMessage = $this->getMockBuilder(MailTemplateMessageBuilder::class)
            ->setMethods(array('renderTemplate'))
            ->getMock();
        $mailTemplateMessage
            ->expects($this->at(0))
            ->method('renderTemplate')
            ->with("{$template}_subject", $parameter)
            ->willReturn('主旨');
        $mailTemplateMessage
            ->expects($this->at(1))
            ->method('renderTemplate')
            ->with("{$template}_message", $parameter)
            ->willReturn('信件內容');

        //act
        /** @var GeneralMailMessage $result */
        $result = $mailTemplateMessage->create($email, $template);

        //assert
        $this->assertInstanceOf(GeneralMailMessage::class, $result);
        $this->assertEquals(array($email), $result->getEmails());
        $this->assertEquals('主旨', $result->getSubject());
        $this->assertEquals('信件內容', $result->getBody());
    }
}