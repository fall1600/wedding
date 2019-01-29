<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/9/14
 * Time: 下午2:32
 */

namespace Backend\BaseBundle\Tests\Service\Mail;


use Backend\BaseBundle\Message\MailMessageInterface;
use Backend\BaseBundle\Service\CustomMailerService;
use Backend\BaseBundle\Service\Mail\SendMailBuilder;
use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;

class SendMailBuilderTest extends BaseKernelTestCase
{
    public function test_injects()
    {
        //arrange
        //act
        $sendMailBuilder = $this->container->get('send_mail_builder');
        //assert
        $this->assertInstanceOf(CustomMailerService::class, $this->getObjectAttribute($sendMailBuilder, 'customMailerService'));
    }

    public function test_send()
    {
        //arrange
        $mailMessageInterface = $this->getMockBuilder(MailMessageInterface::class)
            ->setMethods(array())
            ->getMock();

        $message = $this->getMockBuilder(\Swift_Message::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $sendMailBuilder = $this->getMockBuilder(SendMailBuilder::class)
            ->setMethods(array('compositionMessage', 'doSend'))
            ->getMock();
        $sendMailBuilder
            ->expects($this->once())
            ->method('compositionMessage')
            ->with($mailMessageInterface)
            ->willReturn($message);
        $sendMailBuilder
            ->expects($this->once())
            ->method('doSend')
            ->with($message);

        //act
        $sendMailBuilder->send($mailMessageInterface);

        //assert
    }

    public function test_compositionMessage()
    {
        //arrange
        $sendMailBuilder = $this->container->get('send_mail_builder');
        $mailMessageInterface = $this->getMockBuilder(MailMessageInterface::class)
            ->setMethods(array('getSubject', 'getEmails', 'getBody', 'getCharset', 'getContentType'))
            ->getMock();
        $mailMessageInterface
            ->expects($this->once())
            ->method('getSubject')
            ->willReturn('subject');
        $mailMessageInterface
            ->expects($this->atLeastOnce())
            ->method('getEmails')
            ->willReturn(array('bubble@dgfactor.com'));
        $mailMessageInterface
            ->expects($this->once())
            ->method('getBody')
            ->willReturn('body');
        $mailMessageInterface
            ->expects($this->once())
            ->method('getCharset')
            ->willReturn('utf-8');
        $mailMessageInterface
            ->expects($this->once())
            ->method('getContentType')
            ->willReturn('text/html');

        //act
        $result = $this->callObjectMethod($sendMailBuilder, 'compositionMessage', $mailMessageInterface);

        //assert
        $this->assertInstanceOf(\Swift_Message::class, $result);
        //進swift_Message之後email會變成key，如果有接name，name會變成value
        $this->assertTrue(key_exists('bubble@dgfactor.com', $result->getTo()));
        $this->assertEquals('subject', $result->getSubject());
        $this->assertEquals('body', $result->getBody());
    }

    public function test_doSend()
    {
        //arrange
        $sendMailBuilder = $this->container->get('send_mail_builder');
        $message = $this->getMockBuilder(\Swift_Message::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
        $mailer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function(){
            });
        $mailerService = $this->getMockBuilder(CustomMailerService::class)
            ->setMethods(array('get'))
            ->getMock();
        $mailerService
            ->expects($this->once())
            ->method('get')
            ->willReturn($mailer);
        $this->setObjectAttribute($sendMailBuilder, 'customMailerService', $mailerService);

        //act
        $this->callObjectMethod($sendMailBuilder, 'doSend', $message);

        //assert
    }
}