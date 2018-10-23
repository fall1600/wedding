<?php

namespace Widget\InvitationBundle\Form;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service('form_service')
 */
class FormService
{
    protected function bindObjectWithRawData(\BaseObject $object, $rawData)
    {
        $formData = $this->filterExtraColumn($rawData);
        $formConfig = $this->getFormConfig();
        $form = $this->createForm(
            new BaseFormType(function(FormBuilderInterface $builder, array $options) use($formConfig){
                foreach ($formConfig as $config){
                    $builder
                        ->add($config->getFieldName(), $config->getFieldType(), $config->getOptions());
                }
            }, 'form'),
            $object,
            array('csrf_protection' => false)
        );
        $form->submit($formData, false);
        return $form;
    }

    protected function filterExtraColumn($formData)
    {
        $filteredData = array();
        $formConfig = $this->getFormConfig();
        foreach ($formConfig as $config){
            if(isset($formData[$config->getFieldName()])){
                $filteredData[$config->getFieldName()] = $formData[$config->getFieldName()];
            }
        }
        return $filteredData;
    }
}
