<?php
namespace Backend\BaseBundle\Token\Service;

use Backend\BaseBundle\Token\Service\TokenRequest\TokenRequestInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

/**
 * @DI\Service("token_service.sha")
 */
class ShaTokenService extends AbstractTokenService
{
    protected $secret;
    protected $issuer;

    /**
     * @DI\InjectParams({
     *   "issuer" = @DI\Inject("%token_issuer%")
     * })
     */
    public function injectIssuer($issuer)
    {
        $this->issuer = $issuer;
    }

    /**
     * @DI\InjectParams({
     *   "secret" = @DI\Inject("%secret%")
     * })
     */
    public function injectSecret($secret)
    {
        $this->secret = $secret;
    }

    public function sign(TokenRequestInterface $tokenRequest)
    {
        $signer = new Sha256();
        return (string)$this->applyData($this->issuer, $tokenRequest)
            ->sign($signer, $this->secret)
            ->getToken();
    }

    /**
     * @param $token
     * @return Token|null
     */
    public function parse($jwtToken)
    {
        $result = $this->verify($jwtToken);
        if($result === false){
            return null;
        }
        return new Token($result);
    }

    protected function verify($jwtToken)
    {
        try {
            $time = $this->getCurrentTime();
            $token = (new Parser())->parse($jwtToken);

            $validationData = new ValidationData();
            $validationData->setIssuer($this->issuer);
            $validationData->setCurrentTime($time);

            if (!$token->verify(new Sha256(), $this->secret)) {
                return false;
            }

            if (!$token->validate($validationData)) {
                return false;
            }

            return json_decode(json_encode($token->getClaims()), true);
        }
        catch (\Exception $e){
            return false;
        }
    }
}