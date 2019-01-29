<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/2/15
 * Time: 上午10:51
 */

namespace Backend\BaseBundle\Service;


use Backend\BaseBundle\Model\SiteConfigQuery;
use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @DI\Service("backend_base.send_mail")
 */
class SendMailService
{
    /** @var  CustomMailerService */
    protected $customMailerService;
    /** @var  SiteConfigBuilder */
    protected $siteConfigBuilder;

    /**
     * @InjectParams({
     *    "customMailerService" = @Inject("site_custom_mailer"),
     * })
     */
    public function injectCustomMailerService(CustomMailerService $customMailerService)
    {
        $this->customMailerService = $customMailerService;
    }

    /**
     * @InjectParams({
     *    "siteConfigBuilder" = @Inject("backend_base.site_config_builder"),
     * })
     */
    public function injectSiteConfigBuilder(SiteConfigBuilder $siteConfigBuilder)
    {
        $this->siteConfigBuilder = $siteConfigBuilder;
    }

    protected function twigBuilder(array $templates)
    {
        return new \Twig_Environment(new \Twig_Loader_Array($templates));
    }

    /**
     * service介面
     * @param $group
     * @param $to
     * @param $templateName
     * @param $templateSubject
     * @param $templateBody
     * @param $message
     */
    public function send($group = array(), $to = array(), $templateName, $templateSubject = array(), $templateBody = array(), \Swift_Message $message = null)
    {
//        判斷是否有客制的message
        if($message === null) {
            $message = $this->customMailerService->newMessage();
        }

        //        找config
        $config = $this->siteConfigBuilder->build()->get("{$group['element']}.mail")[$group['name']];

//        畫樣板
        $mailerTwig = $this->twigBuilder(array(
            "{$templateName}_mail_subject" => $config["{$templateName}_mail_subject"],
            "{$templateName}_mail" => $config["{$templateName}_mail"],
        ));
        $message
            ->setSubject($mailerTwig->render("{$templateName}_mail_subject", $templateSubject))
            ->setTo(
                $to['email'],
                $to['name']
            )
            ->setBody($mailerTwig->render("{$templateName}_mail", $templateBody))
            ->setCharset('utf-8')
            ->setContentType('text/html')
        ;
//        寄信
        $this->sendMessage($message);
    }

    /**
     * @param $message
     */
    protected function sendMessage($message)
    {
        try {
            return $this->customMailerService->get()->send($message);
        } catch (\Exception $e) {
            return false;
        }
    }
}