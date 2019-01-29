<?php
namespace Backend\BaseBundle\Controller\API;

use Backend\BaseBundle\Event\SystemSiteConfigEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/config")
 */
class ConfigController extends BaseController
{
    /**
     * @var EventDispatcher
     * @DI\Inject()
     */
    protected $eventDispatcher;

    /**
     * @Route("/{config}")
     * @Method({"GET"})
     * @Cache(expires="tomorrow", public=true)
     */
    public function indexAction($config)
    {
        $event = new SystemSiteConfigEvent($config);
        $this->eventDispatcher->dispatch(SystemSiteConfigEvent::EVENT_NAME, $event);
        if(!$event->isAllow()){
            return $this->createJsonResponse('', Response::HTTP_NOT_FOUND);
        }

        $content = $this->get('backend_base.site_config_builder')->build()->get('system');

        return $this->createJsonResponse(array(
            'config' => isset($content[$config])?$content[$config]:null,
        ));
    }
}