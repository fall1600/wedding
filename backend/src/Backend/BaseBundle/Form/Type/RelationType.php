<?php
namespace Backend\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Propel\Bundle\PropelBundle\Form\Type\ModelType;

/**
 * @Service
 * @Tag("form.type", attributes = {"alias": "relation"})
 */
class RelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(is_callable($options['preSetData'])) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, $options['preSetData']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'multiple' => true,
            'query' => function(Options $options){
                $queryClass = $options['class'].'Query';
                return $queryClass::create();
            },
            'preSetData' => null,
        ));
    }

    public function getParent()
    {
        return ModelType::class;
    }

}