<?php
namespace Backend\BaseBundle\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("api.validator.error.convertor")
 */
class ConstraintViolationListConvertor
{
    public function toArray(ConstraintViolationListInterface $errors)
    {
        $errorMessage = array();
        foreach($errors as $error) {
            preg_match_all('/\[(.*?)\]/i', $error->getPropertyPath(), $matches);
            $errorMessage = $this->convertPath($errorMessage, $matches[1], $error->getMessage());
        }
        return $errorMessage;
    }

    protected function convertPath($errorMessages, $paths, $errorMessage)
    {
        $currentNode = &$errorMessages;
        foreach ($paths as $path){
            if(!isset($currentNode[$path]) || !is_array($currentNode[$path])){
                $currentNode[$path] = array();
            }
            $currentNode = &$currentNode[$path];
        }
        $currentNode = $errorMessage;
        return $errorMessages;
    }

    public function toJson(ConstraintViolationListInterface $errors)
    {
        return json_encode($this->toArray($errors));
    }
}