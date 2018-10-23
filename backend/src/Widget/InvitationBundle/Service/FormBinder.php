<?php

namespace Widget\InvitationBundle\Service;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Form\Type\BaseFormType;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;

trait FormBinder
{
    /** @var FormFactory */
    protected $formFactory;

    /**
     * @DI\InjectParams({
     *   "formFactory" = @DI\Inject("form.factory")
     * })
     */
    public function injectFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    protected function bindObjectWithRawData(\BaseObject $object, $rawData)
    {
        $formData = $this->filterExtraColumn($rawData);
        $formConfig = $this->getFormConfig();
        $form = $this->formFactory->create(
            new BaseFormType(function(FormBuilderInterface $builder, array $options) use($formConfig) {
                /** @var APIFormTypeItem $config */
                foreach ($formConfig as $config) {
                    $builder->add($config->getFieldName(), $config->getFieldType(), $config->getOptions());
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
        /** @var APIFormTypeItem $config */
        foreach ($formConfig as $config) {
            if(isset($formData[$config->getFieldName()])) {
                $filteredData[$config->getFieldName()] = $formData[$config->getFieldName()];
            }
        }
        return $filteredData;
    }
}
