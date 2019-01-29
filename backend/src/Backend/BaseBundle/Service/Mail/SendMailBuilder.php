<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/9/14
 * Time: 上午11:30
 */

namespace Backend\BaseBundle\Service\Mail;


use Backend\BaseBundle\Message\MailMessageInterface;
use Backend\BaseBundle\Service\CustomMailerService;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("send_mail_builder")
 */
class SendMailBuilder
{
    /** @var  CustomMailerService */
    protected $customMailerService;

    /**
     * @DI\InjectParams({
     *    "customMailerService" = @DI\Inject("site_custom_mailer"),
     * })
     */
    public function injectCustomMailerService(CustomMailerService $customMailerService)
    {
        $this->customMailerService = $customMailerService;
    }

    /**
     * @param MailMessageInterface $mailMessage
     * @param \Swift_Message|null $message
     * @return bool|int
     */
    public function send(MailMessageInterface $mailMessage, \Swift_Message $message = null)
    {
        $message = $this->compositionMessage($mailMessage, $message);
        return $this->doSend($message);
    }

    /**
     * @param MailMessageInterface $mailMessage
     * @param \Swift_Message|null $message
     * @return $message
     */
    protected function compositionMessage(MailMessageInterface $mailMessage, \Swift_Message $message = null)
    {
//        判斷是否有客制的message
        if ($message === null) {
            $message = $this->customMailerService->newMessage();
        }

        return $message
            ->setSubject($mailMessage->getSubject())
            ->setTo($mailMessage->getEmails())
            ->setBody($mailMessage->getBody())
            ->setCharset($mailMessage->getCharset())
            ->setContentType($mailMessage->getContentType());
    }

    /**
     * @param \Swift_Message $message
     * @return bool|int
     */
    protected function doSend(\Swift_Message $message)
    {
        try {
            return $this->customMailerService->get()->send($message);
        } catch (\Exception $e) {
            return false;
        }
    }
}