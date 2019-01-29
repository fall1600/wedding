<?php
namespace Widget\PhotoBundle\Service;


use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\Serializer\Context;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\VisitorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Widget\PhotoBundle\Image\Resolver;
use Widget\PhotoBundle\Model\Photo;

/**
 * @Service()
 * @Tag("jms_serializer.handler", attributes = {"public": false, "type": Photo::class, "format": "json", "method": "onPhotoSerialize"})
 */
class PhotoAccess
{
    /** @var  Resolver */
    protected $resolver;

    /** @var  AssetsHelper */
    protected $assetsHelper;

    /**
     * @InjectParams({
     *    "resolver" = @Inject("widget_photo.image.resolver"),
     *    "assetsHelper" = @Inject("templating.helper.assets"),
     * })
     */
    public function injectService(Resolver $resolver, AssetsHelper $assetsHelper)
    {
        $this->resolver = $resolver;
        $this->assetsHelper = $assetsHelper;
    }

    public function onPhotoSerialize(VisitorInterface $visitor, Photo $photo, array $type, Context $context)
    {
        return $visitor->visitArray($this->makePhotoPath($photo), $type, $context);
    }

    protected function makePhotoPath(Photo $photo)
    {
        foreach(array_keys($photo->getInfo()) as $suffix){
            $photoUrls[$suffix] = $this->assetsHelper->getUrl($this->resolver->resolveWebPath($photo, $suffix), 'photo');
        }
        $photoUrls['_uid'] = $photo->getUid();
        return $photoUrls;
    }
}