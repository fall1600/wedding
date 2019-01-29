<?php
namespace Backend\BaseBundle\Controller\API;

use Backend\BaseBundle\Model\Site;
use Backend\BaseBundle\Response\APIJsonResponse;
use Backend\BaseBundle\Service\ServerSideRender;
use JMS\DiExtraBundle\Annotation\Inject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Route("/seo")
 */
class SEOController extends BaseController
{
    /**
     * @var ServerSideRender
     * @Inject()
     */
    protected $serverSideRender;

    /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        $url = $request->get('url');
        $engine = $request->get('e', $this->getParameter('ssr_engine'));
        return $this->createJsonResponse($this->serverSideRender->render($url, $engine));
    }

}