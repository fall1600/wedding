<?php
namespace Backend\BaseBundle\Tests\EventListener;

use Backend\BaseBundle\EventListener\I18nLocaleRequestListener;
use Backend\BaseBundle\Propel\Behavior\I18nBehavior;
use Backend\BaseBundle\Propel\Behavior\NullI18nBehavior;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class I18nLocaleRequestListenerTest extends BaseTestCase
{
    /**
     * 沒有啟用語系的情況
     */
    public function test_injectI18nBehavior_NULLI18nBehavior()
    {
        //arrange
        $listener = new I18nLocaleRequestListener();
        $behaviorClass = NullI18nBehavior::class;

        //act
        $listener->injectI18nBehavior($behaviorClass);

        //assert
        $this->assertFalse($this->getObjectAttribute($listener, 'isI18nEnable'));
    }

    /**
     * 有啟用語系的情況
     */
    public function test_injectI18nBehavior_18nBehavior()
    {
        //arrange
        $listener = new I18nLocaleRequestListener();
        $behaviorClass = I18nBehavior::class;

        //act
        $listener->injectI18nBehavior($behaviorClass);

        //assert
        $this->assertTrue($this->getObjectAttribute($listener, 'isI18nEnable'));
    }

    /**
     * 沒啟用語系的情況，也沒有帶語系
     */
    public function test_onLocaleRequest_i18n_disable_with_no_locale_request()
    {
        //arrange
        $expectedResult = null;
        $locale = null;
        $i18nEnable = false;
        $listener = new I18nLocaleRequestListener();
        $this->setObjectAttribute($listener, 'isI18nEnable', $i18nEnable);
        $request = new Request();

        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest'))
            ->getMock();
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        //act
        $listener->onLocaleRequest($event);

        //assert
        $this->assertNull($request->query->get('_locale'));
    }

    /**
     * 沒有啟用語系的情況，但是有帶語系
     */
    public function test_onLocaleRequest_i18n_disable_with_locale_request()
    {
        //arrange
        $expectedResult = 'test';
        $locale = 'test';
        $i18nEnable = false;
        $listener = new I18nLocaleRequestListener();
        $this->setObjectAttribute($listener, 'isI18nEnable', $i18nEnable);
        $request = new Request();
        $request->query->set('_locale', $locale);
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest'))
            ->getMock();
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        //act
        $listener->onLocaleRequest($event);

        //assert
        $this->assertEquals($expectedResult, $request->query->get('_locale'));
    }

    /**
     * 啟用語系的情況，沒有帶語系
     */
    public function test_onLocaleRequest_i18n_enable_with_no_locale_request()
    {
        //arrange
        $expectedResult = 'en_US';
        $locale = null;
        $i18nEnable = true;
        $listener = new I18nLocaleRequestListener();
        $this->setObjectAttribute($listener, 'isI18nEnable', $i18nEnable);
        $request = new Request();
        $request->query->set('_locale', $locale);
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest'))
            ->getMock();
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        //act
        $listener->onLocaleRequest($event);

        //assert
        $this->assertEquals($expectedResult, $request->query->get('_locale'));
    }

    /**
     * 有啟用語系的情況，也有帶語系
     */
    public function test_onLocaleRequest_i18n_enable_with_has_locale_request()
    {
        //arrange
        $expectedResult = 'zh_TW';
        $locale = 'zh_TW';
        $i18nEnable = true;
        $listener = new I18nLocaleRequestListener();
        $this->setObjectAttribute($listener, 'isI18nEnable', $i18nEnable);
        $request = new Request();
        $request->query->set('_locale', $locale);
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest'))
            ->getMock();
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        //act
        $listener->onLocaleRequest($event);

        //assert
        $this->assertEquals($expectedResult, $request->query->get('_locale'));
    }
}