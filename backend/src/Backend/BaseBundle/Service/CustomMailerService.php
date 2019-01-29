<?php
namespace Backend\BaseBundle\Service;

use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * @Service("site_custom_mailer")
 */
class CustomMailerService
{
    /** @var  \Swift_Mailer */
    protected $mailer;

    /** @var  SiteConfigBuilder */
    protected $siteConfigBuilder;

    /**
     * @InjectParams({
     *    "mailer" = @Inject("swiftmailer.mailer.site"),
     * })
     */
    public function injectMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @InjectParams({
     *    "siteConfigBuilder" = @Inject("backend_base.site_config_builder"),
     * })
     */
    public function injectSiteConfigBuilder($siteConfigBuilder)
    {
        $this->siteConfigBuilder = $siteConfigBuilder;
    }

    /**
     * @return \Swift_Mailer
     */
    public function get()
    {
        $siteConfig = $this->siteConfigBuilder->build();
        $config = $siteConfig->get('mail');
        $transport = $this->mailer->getTransport();
        if($transport instanceof \Swift_Transport_EsmtpTransport) {
            $transport
                ->setHost(isset($config['host']) ? $config['host'] : null)
                ->setPort(isset($config['port']) ? $config['port'] : 25)
                ->setEncryption(isset($config['encrypt']) ? $config['encrypt'] : null)
                ->setUsername(isset($config['account']) ? $config['account'] : null)
                ->setPassword(isset($config['password']) ? $config['password'] : null);
        }
        return $this->mailer;
    }

    /**
     * @return \Swift_Message
     */
    public function newMessage()
    {
        $siteConfig = $this->siteConfigBuilder->build();
        $config = $siteConfig->get('mail');
        $message = \Swift_Message::newInstance();
        $message->setFrom($config['sender_mail'], $config['sender_name']);
        if(isset($config['reply_to'])){
            $message->setReplyTo($config['reply_mail'], $config['reply_name']);
        }
        return $message;
    }
}