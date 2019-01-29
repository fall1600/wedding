<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/6/1
 * Time: 下午5:36
 */

namespace Backend\BaseBundle\Service\Mail;

use Backend\BaseBundle\Message\GeneralMailMessage;
use Backend\BaseBundle\Message\MailMessageInterface;
use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("mail_template_message_builder")
 */
class MailTemplateMessageBuilder
{
    /** @var  SiteConfigBuilder */
    protected $siteConfigBuilder;

    /**
     * @DI\InjectParams({
     *    "siteConfigBuilder" = @DI\Inject("backend_base.site_config_builder"),
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
     * @param $email
     * @param $templateName
     * @param array $parameter
     * @return MailMessageInterface
     */
    public function create($email, $templateName, $parameter = array())
    {
        return $this->createFromMany(array($email), $templateName, $parameter);
    }

    /**
     * @param $emails
     * @param $templateName
     * @param array $parameter
     * @return MailMessageInterface
     */
    public function createFromMany(array $emails, $templateName, $parameter = array())
    {
        $subject = $this->renderTemplate("{$templateName}_subject", $parameter);
        $body = $this->renderTemplate("{$templateName}_message", $parameter);
        return new GeneralMailMessage($emails, $subject, $body);
    }

    protected function renderTemplate($templateName, $parameter)
    {
        //上層傳下來的TEMPLATE_NAME
        //找config設定
        $config = $this->siteConfigBuilder->build()->get('widget_member');
        //畫樣板
        $twig = $this->twigBuilder($config);
        //組訊息
        return $twig->render($templateName, $parameter);
    }
}