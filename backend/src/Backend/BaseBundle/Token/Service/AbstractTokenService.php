<?php
namespace Backend\BaseBundle\Token\Service;

use Backend\BaseBundle\Token\Service\TokenRequest\TokenRequestInterface;
use Lcobucci\JWT\Builder;

abstract class AbstractTokenService implements TokenServiceInterface
{
    protected function getCurrentTime()
    {
        return time();
    }

    /**
     * @param TokenRequestInterface $tokenRequest
     * @param $time
     * @return Builder
     */
    protected function applyData($issuer, TokenRequestInterface $tokenRequest)
    {
        $time = $this->getCurrentTime();
        $builder = new Builder();
        return $builder
            ->setIssuer($issuer)
            ->setExpiration($time + $tokenRequest->getTtl())
            ->setIssuedAt($time)
            ->set('id', $tokenRequest->getId())
            ->set('type', $tokenRequest->getType())
            ->set('data', $tokenRequest->getData());
    }


}