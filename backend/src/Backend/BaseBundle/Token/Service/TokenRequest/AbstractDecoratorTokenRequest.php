<?php
namespace Backend\BaseBundle\Token\Service\TokenRequest;


abstract class AbstractDecoratorTokenRequest implements TokenRequestInterface
{
    /** @var TokenRequestInterface  */
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

    public function getData()
    {
        return $this->tokenRequest->getData();
    }

    public function getTtl()
    {
        return $this->tokenRequest->getTtl();
    }
}