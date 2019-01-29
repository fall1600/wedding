<?php
namespace Widget\PhotoBundle\Form\Type;


use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Widget\PhotoBundle\Form\Transformer\ModelPhotoListTransformer;
use Widget\PhotoBundle\Image\PhotoList;

/**
 * @DI\Service()
 * @Tag("form.type", attributes = {"alias": "photo_list"})
 */
class APIPhotoListType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ModelPhotoListTransformer());
        $builder->setCompound(false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

}