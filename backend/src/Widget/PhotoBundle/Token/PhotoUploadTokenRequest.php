<?php
namespace Widget\PhotoBundle\Token;

use Backend\BaseBundle\Token\Service\TokenRequest\TokenRequestInterface;

class PhotoUploadTokenRequest implements TokenRequestInterface
{
    protected $tokenRequest;

    public function __construct(TokenRequestInterface $tokenRequest)
    {
        $this->tokenRequest = $tokenRequest;
    }

    public function getId()
    {
        return $this->tokenRequest->getId();
    }

    public function getType()
    {
        return $this->tokenRequest->getType();
    }

    public function getPayload()
    {
        return $this->tokenRequest->getPayload();
    }

    public function getData()
    {
        $data = $this->tokenRequest->getData();
        $data['photo'] = true;
        return $data;
    }

    public function getTtl()
    {
        return $this->tokenRequest->getTtl();
    }
}