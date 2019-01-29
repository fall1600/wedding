<?php
namespace Widget\PhotoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as BaseType;
use Widget\PhotoBundle\Form\Transformer;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Widget\PhotoBundle\Image\PhotoConfigFinder;
use Widget\PhotoBundle\Image\Resizer;

/**
 * @Service
 * @Tag("form.type", attributes = {"alias": "photo_upload"})
 */
class PhotoUploadType extends AbstractType
{
    /** @var  Resizer */
    protected $resizer;

    /** @var  PhotoConfigFinder */
    protected $configFinder;

    /**
     * @InjectParams({
     *     "resizer" = @Inject("widget_photo.image.resizer"),
     * })
     */
    public function injectResizer(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }


    /**
     * @InjectParams({
     *     "configFinder" = @Inject("widget.photo_bundle.config_finder"),
     * })
     */
    public function injectPhotoConfigFinder(PhotoConfigFinder $configFinder)
    {
        $this->configFinder = $configFinder;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'photo_config' => 'default',
            'with_crop' => false,
            'post_update' => null,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->configFinder->findConfig($options['photo_config']);
        $builder->setAttribute('with_crop', $config->getCrop());
        $builder->addViewTransformer(new Transformer\ViewPhotoTransformer($this->resizer, $config , $config->getCrop()));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['with_crop'] = $form->getConfig()->getAttribute('with_crop');
    }

    public function getParent()
    {
        return BaseType\FileType::class;
    }

}