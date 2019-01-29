<?php

namespace Widget\PhotoBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\API\BaseController;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Widget\PhotoBundle\Image\Uploader;

/**
 * @Route("/photo")
 */
class PhotoController extends BaseController
{
    /**
     * @var Uploader
     * @DI\Inject("widget_photo.image.uploader")
     */
    protected $uploader;

    /**
     * @Security("has_role_or_superadmin('ROLE_PHOTO_WRITE')")
     * @Route("/{name}")
     * @Method({"POST"})
     */
    public function uploadAction(Request $request, $name)
    {
        $uploadFiles = $request->files->all();

        if(count($uploadFiles) == 0 || count($uploadFiles) > 1){
            return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
        }

        /** @var UploadedFile $uploadFile */
        $uploadFile = array_pop($uploadFiles);
        $photo = $this->uploader->makePhoto($uploadFile, $name);
        $photo->save();
        return $this->createJsonSerializeResponse($photo, array('detail'));
    }

}
