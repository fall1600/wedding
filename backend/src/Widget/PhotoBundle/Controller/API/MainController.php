<?php
namespace Widget\PhotoBundle\Controller\API;

use Backend\BaseBundle\Controller\API\BaseController;
use Backend\BaseBundle\Token\Service\Token;
use JMS\DiExtraBundle\Annotation\Inject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;
use Backend\BaseBundle\Model;
use Backend\BaseBundle\Event\RoleTypeEvent;
use Widget\MemberBundle\Token\MemberAuthToken;
use Widget\PhotoBundle\Image\Uploader;
use Widget\PhotoBundle\Model as PhotoModel;

/**
 *
 * @Route("/photo")
 */
class MainController extends BaseController
{
    /**
     * @var Uploader
     * @Inject("widget_photo.image.uploader")
     */
    protected $uploader;

    /**
     * @Route("/{name}")
     * @Method({"POST"})
     */
    public function uploadAction(Request $request, $name)
    {
        $memberAuthToken = $request->attributes->get('_authorizedToken');

        if(!$this->isValidToken($memberAuthToken)){
            return $this->createHttpExceptionResponse(Response::HTTP_UNAUTHORIZED);
        }

        if(!$this->canUploadPhoto($memberAuthToken)){
            return $this->createHttpExceptionResponse(Response::HTTP_FORBIDDEN);
        }

        $memberId = $this->extractMemberId($memberAuthToken);

        return $this->uploadPhoto($request, $name, $memberId);
    }

    /**
     * @param Request $request
     * @param $name
     * @param $memberId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function uploadPhoto(Request $request, $name, $memberId)
    {
        $uploadFiles = $request->files->all();

        if (count($uploadFiles) == 0 || count($uploadFiles) > 1) {
            return $this->createHttpExceptionResponse(Response::HTTP_BAD_REQUEST);
        }

        /** @var UploadedFile $uploadFile */
        $uploadFile = array_pop($uploadFiles);
        $photo = $this->uploader->makePhoto($uploadFile, $name);
        $photo->setMemberId($memberId);
        $photo->save();
        return $this->createJsonSerializeResponse($photo, array('detail'));
    }

    /**
     * @param $memberAuthToken
     * @return mixed
     */
    protected function extractMemberId($memberAuthToken)
    {
        if($memberAuthToken instanceof MemberAuthToken){
            return $memberAuthToken->getAuthData()['uid'];
        }

        if($memberAuthToken instanceof Token){
            return $memberAuthToken->getId();
        }

        return null;
    }

    protected function isValidToken($memberAuthToken)
    {
        if($memberAuthToken instanceof MemberAuthToken){
            return true;
        }

        if($memberAuthToken instanceof Token){
            return true;
        }

        return false;
    }

    protected function canUploadPhoto($memberAuthToken)
    {
        if($memberAuthToken instanceof MemberAuthToken){
            return $memberAuthToken->get('uploadphoto');
        }

        if($memberAuthToken instanceof Token){
            return $memberAuthToken->getData()['photo']??false;
        }

        return false;
    }
}