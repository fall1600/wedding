<?php
namespace Backend\BaseBundle\Controller\API;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CORSController extends Controller
{

    /**
     * @Route("/{any}", requirements={"any" = ".*"})
     * @Method({"OPTIONS"})
     */
    public function optionsAction()
    {
        return new Response();
    }

}