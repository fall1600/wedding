<?php
namespace Backend\BaseBundle\Form\Type;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class APIFormTypeItem
{
    protected $fieldName;
    protected $fieldType = HiddenType::class;
    protected $options = array();

    public function __construct($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return mixed
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @param mixed $fieldType
     * @return APIFormTypeItem
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
        return $this;
    }

    /**
     * @param array $options
     * @return APIFormTypeItem
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}