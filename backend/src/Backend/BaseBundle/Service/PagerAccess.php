<?php
namespace Backend\BaseBundle\Service;


use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use PropelModelPager;

/**
 * @Service("backend_base_pager.access")
 * @Tag("jms_serializer.handler", attributes = {"public": false, "type": PropelModelPager::class, "format": "json", "method": "onPagerSerialize"})
 */
class PagerAccess
{

    public function onPagerSerialize(VisitorInterface $visitor, \PropelModelPager $pager, array $type, Context $context)
    {
        return $visitor->visitArray(array(
            'pager' => array(
                'page' => $pager->getPage(),
                'pages' => $pager->getLastPage(),
                'rows' => $pager->getNbResults(),
            ),
            'data' => $pager->getResults(),
        ), $type, $context);
    }

}