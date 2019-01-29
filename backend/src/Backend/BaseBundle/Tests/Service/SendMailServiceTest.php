<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/2/15
 * Time: 下午2:50
 */

namespace Backend\BaseBundle\Tests\Service;

use Backend\BaseBundle\Service\CustomMailerService;
use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;

class SendMailServiceTest extends BaseKernelTestCase
{
    public function test_send()
    {
        //arrange
        $group = array(
            'element' => 'sunny_send',
            'name'  => 'SunnySend'
        );
        $to = array(
            'email' => array(
                'bubble@dgfactor.com',
            ),
            'name' => array(
                'bubble'
            )
        );
        $templateName = 'sunny_send';
        $templateSubject = array(
            'testName' => 'bubble',
        );
        $templateBody = array(
            'sunny' => '晴天'
        );

        $service = $this->container->get('backend_base.send_mail');

        $message = $this->getMockBuilder(\Swift_Message::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setSubject', 'setTo', 'setBody', '__destruct', 'setCharset', 'setContentType'))
            ->getMock();

        $message
            ->expects($this->once())
            ->method('setCharset')
            ->willReturnCallback(function($charset) use($message){
                $this->assertEquals('utf-8', $charset);
                return $message;
            });
        $message
            ->expects($this->once())
            ->method('setContentType')
            ->willReturnCallback(function($type) use($message){
                $this->assertEquals('text/html', $type);
                return $message;
            });
        $message
            ->expects($this->once())
            ->method('setSubject')
            ->willReturnCallback(function($subject) use($message){
                $this->assertEquals("你好 bubble", $subject);
                return $message;
            });
        $message
            ->expects($this->once())
            ->method('setTo')
            ->willReturnCallback(function($receviver) use($message, $to){
                $this->assertEquals(
                    $to['email'],
                    $receviver
                );
                return $message;
            });
        $message
            ->expects($this->once())
            ->method('setBody')
            ->willReturnCallback(function($content) use($message){
                $this->assertEquals("今天天氣 :晴天", $content);
                return $message;
            });
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
            ->setMethods(array('get', 'newMessage'))
            ->getMock();
        $mailerService
            ->expects($this->once())
            ->method('get')
            ->willReturn($mailer);
        $mailerService
            ->expects($this->once())
            ->method('newMessage')
            ->willReturn($message);
        //act
        $this->setObjectAttribute($service, 'customMailerService', $mailerService);

        $service->send($group, $to, $templateName, $templateSubject, $templateBody);

        //assert
    }
}