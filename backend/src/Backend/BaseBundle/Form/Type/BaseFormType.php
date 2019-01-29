<?php
namespace Backend\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class BaseFormType extends AbstractType
{
    protected $builderCallback;
    protected $name;

    public function __construct($builderCallback, $name)
    {
        $this->builderCallback = $builderCallback;
        $this->name = $name;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $callback = $this->builderCallback;
        $callback($builder, $options);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBlockPrefix()
    {
        return $this->name;
    }
}
