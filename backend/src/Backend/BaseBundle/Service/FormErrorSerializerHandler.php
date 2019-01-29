<?php
namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;

/**
 * @Service()
 * @Tag("jms_serializer.handler", attributes = {"public": false, "type": FormErrorIterator::class, "format": "json", "method": "onFormErrorIterator"})
 */
class FormErrorSerializerHandler
{
    public function onFormErrorIterator(VisitorInterface $visitor, FormErrorIterator $errors, array $type, Context $context)
    {
        $message = $this->serializeFormErrorIterator($errors);
        return $visitor->visitArray($message, $type, $context);
    }

    protected function serializeFormErrorIterator(FormErrorIterator $errors, $message = array())
    {
        foreach ($errors as $error){
            if($error instanceof FormError) {
                $levels = $this->getLevels($error->getOrigin());
                $message = $this->setMessageValue($message, $levels, $error->getMessage());
            }
            if($error instanceof FormErrorIterator){
                $message = $this->serializeFormErrorIterator($error, $message);
            }
        }
        return $message;
    }

    protected function getLevels(FormInterface $form)
    {
        $levels = array();
        while (!$form->isRoot()) {
            array_unshift($levels, $form->getName());
            $form = $form->getParent();
        }
        return $levels;
    }

    protected function setMessageValue(array $message, array $levels, $value)
    {
        $field = &$message;
        foreach ($levels as $level){

            if(!isset($field[$level])){
                $field[$level] = array();
            }

            $field = &$field[$level];
        }
        $field[] = $value;
        return $message;
    }
}
