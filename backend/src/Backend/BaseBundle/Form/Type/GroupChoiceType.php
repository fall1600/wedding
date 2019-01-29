<?php
namespace Backend\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as BaseType;

/**
 * @Service
 * @Tag("form.type", attributes = {"alias": "group_choice"})
 */
class GroupChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'group_choices' => array(),
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('group_choices', $options['group_choices']);
        $choiceOptions = $options;
        unset($choiceOptions['group_choices']);
        $this->addSubForms($builder, $options['group_choices'], $choiceOptions);
    }

    private function addSubForms(FormBuilderInterface $builder, array $groupChoices, array $options)
    {
        foreach ($groupChoices as $group => $choices) {
            $this->addSubForm($builder, $group, $choices, $options);
        }
    }

    private function addSubForm(FormBuilderInterface $builder, $name, $choices, array $options)
    {
        $name = str_replace('.', '_', $name);
        $options['label'] = $name;
        $builder->add($name, 'choice', $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['group_choices'] = $form->getConfig()->getAttribute('group_choices');
    }

    public function getParent()
    {
        return BaseType\ChoiceType::class;
    }

}