<?php
namespace Backend\BaseBundle\EventListener;


use Backend\BaseBundle\Propel\Behavior\NullI18nBehavior;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @DI\Service()
 */
class I18nLocaleRequestListener
{

    /** @var  bool */
    protected $isI18nEnable;

    /**
     * @InjectParams({
     *     "behaviorClass" = @Inject("%i18n_behavior%")
     * })
     */
    public function injectI18nBehavior($behaviorClass)
    {
        $this->isI18nEnable = $behaviorClass != NullI18nBehavior::class;
    }

    /**
     * @DI\Observe("kernel.request")
     */
    public function onLocaleRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if($this->isI18nEnable){
            $locale = $request->query->get('_locale');
            if($locale == null){
                $request->query->set('_locale', 'en_US');
            }
        }
    }
}