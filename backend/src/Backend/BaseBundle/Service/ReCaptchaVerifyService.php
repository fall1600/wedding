<?php
namespace Backend\BaseBundle\Service;


use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use GuzzleHttp\Client;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @DI\Service("backend_base.recaptcha_verify")
 */
class ReCaptchaVerifyService
{
    /** @var  SiteConfigBuilder */
    protected $siteConfigBuilder;

    /**
     * @InjectParams({
     *    "siteConfigBuilder" = @Inject("backend_base.site_config_builder"),
     * })
     */
    public function injectSiteConfigBuilder(SiteConfigBuilder $siteConfigBuilder)
    {
        $this->siteConfigBuilder = $siteConfigBuilder;
    }

    public function verify($recaptcha)
    {
        $siteConfig = $this->siteConfigBuilder->build()->get('system');
        $siteverifyUrl = "https://www.google.com/recaptcha/api/siteverify";
        $queryArray = array(
            "secret" => $siteConfig['recaptcha_secret_key'],
            "response" => $recaptcha
        );
        $client = new Client();
        $res = $client->request('GET', $siteverifyUrl, array('query' => $queryArray));
        $data = json_decode($res->getBody(), true);
        return $data['success'];
    }
}