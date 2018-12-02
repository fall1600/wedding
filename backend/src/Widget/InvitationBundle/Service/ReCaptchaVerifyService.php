<?php

namespace Widget\InvitationBundle\Service;

use Backend\BaseBundle\Service\ReCaptchaVerifyService as BaseRecaptchaVerifyService;
use JMS\DiExtraBundle\Annotation as DI;
use GuzzleHttp\Client;

/**
 * @DI\Service("widget_invitation.recaptcha_verify")
 */
class ReCaptchaVerifyService extends BaseRecaptchaVerifyService
{
    public function verify($recaptcha)
    {
        $siteConfig = $this->siteConfigBuilder->build()->get('system');
        $siteverifyUrl = "https://www.google.com/recaptcha/api/siteverify";
        $queryArray = array(
            "secret" => $siteConfig['recaptcha_secret_key'],
            "response" => $recaptcha
        );
        $client = new Client();
        $res = $client->request('POST', $siteverifyUrl, array('query' => $queryArray));
        $data = json_decode($res->getBody(), true);
        return $data['success'];
    }
}
