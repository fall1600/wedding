<?php
namespace Widget\PhotoBundle\Form\Type;


use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Widget\PhotoBundle\Form\Transformer\ModelPhotoTransformer;
use Widget\PhotoBundle\Model\Photo;

/**
 * @DI\Service()
 * @Tag("form.type", attributes = {"alias": "photo"})
 */
class APIPhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ModelPhotoTransformer());
        $builder->setCompound(false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Photo::class
        ));
    }

}