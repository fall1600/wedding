<?php

namespace Backend\BaseBundle\Controller\BackendAPI;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/setup")
 * @Security("has_role_or_superadmin('ROLE_SETUP')")
 */
class SiteConfigController  extends BaseBackendAPIController
{
    protected function getFormConfig()
    {
        return array(
            new APIFormTypeItem('config')
        );
    }

    /**
     * 讀取
     * @Route("/{name}")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_SETUP')")
     */
    public function readAction($name)
    {
        return $this->readSiteConfig($name);
    }

    /**
     * 更新
     * @Route("/{name}")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_SETUP')")
     */
    public function updateAction($name, Request $request)
    {
        return $this->updateSiteConfig($name, $request->getContent());
    }
}