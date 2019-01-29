<?php
namespace Backend\BaseBundle\Tests\EventListener;


use Backend\BaseBundle\EventListener\APICORSResponseListener;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @group unit
 */
class APICORSResponseListenerTest extends BaseTestCase
{
    public function test_onRequest_not_master_request()
    {
        //arrange
        $listener = new APICORSResponseListener();
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->setMethods(array('getRequest'))
            ->disableOriginalConstructor()
            ->getMock();
        $event
            ->expects($this->never())
            ->method('getRequest')
            ;
        $request = new Request();
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::SUB_REQUEST, KernelEvent::class);

        //act
        $listener->onRequest($event);

        //assert
        $this->assertNull($request->attributes->get('_cors'));
        $this->assertNull($event->getResponse());
    }

    public function test_onRequest_null_origin()
    {
        //arrange
        $listener = new APICORSResponseListener();
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $request = new Request();
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::MASTER_REQUEST, KernelEvent::class);

        //act
        $listener->onRequest($event);

        //assert
        $this->assertNull($request->attributes->get('_cors'));
        $this->assertNull($event->getResponse());
    }

    public function test_onRequest_invalid_origin()
    {
        //arrange
        $listener = $this->getMockBuilder(APICORSResponseListener::class)
            ->setMethods(array('isValidOrigin'))
            ->disableOriginalConstructor()
            ->getMock();
        $listener
            ->expects($this->once())
            ->method('isValidOrigin')
            ->willReturnCallback(function($origin){
                $this->assertEquals('http://localhost', $origin);
                return false;
            });
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $request = new Request();
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::MASTER_REQUEST, KernelEvent::class);
        $request->headers->set('Origin', 'http://localhost');

        //act
        $listener->onRequest($event);
        $response = $event->getResponse();

        //assert
        $this->assertNull($request->attributes->get('_cors'));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_onRequest_valid_origin()
    {
        //arrange
        $listener = $this->getMockBuilder(APICORSResponseListener::class)
            ->setMethods(array('isValidOrigin'))
            ->disableOriginalConstructor()
            ->getMock();
        $listener
            ->expects($this->once())
            ->method('isValidOrigin')
            ->willReturnCallback(function($origin){
                $this->assertEquals('http://localhost', $origin);
                return true;
            });
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $request = new Request();
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::MASTER_REQUEST, KernelEvent::class);
        $request->headers->set('Origin', 'http://localhost');

        //act
        $listener->onRequest($event);
        $response = $event->getResponse();
        $cors = $request->attributes->get('_cors');

        //assert
        $this->assertEquals(array(
            'Access-Control-Allow-Origin' => 'http://localhost',
            'Access-Control-Allow-Methods' => 'PUT, GET, POST, DELETE, PATCH, OPTIONS',
            'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Accept, Authorization',
            'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Type',
            'Access-Control-Max-Age' => 86400,
        ), $cors);
        $this->assertNull($event->getResponse());
    }

    public function test_onResponse_not_master_request()
    {
        //arrange
        $listener = new APICORSResponseListener();
        $event = $this->getMockBuilder(FilterResponseEvent::class)
            ->setMethods(array('getRequest'))
            ->disableOriginalConstructor()
            ->getMock();
        $event
            ->expects($this->never())
            ->method('getRequest')
        ;
        $request = new Request();
        $request->attributes->set('_cors', array(
            'Access-Control-Allow-Origin' => 'http://localhost',
            'Access-Control-Allow-Methods' => 'PUT, GET, POST, DELETE, OPTIONS',
        ));
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::SUB_REQUEST, KernelEvent::class);

        $response = new Response();
        $this->setObjectAttribute($event, 'response', $response, FilterResponseEvent::class);

        //act
        $listener->onResponse($event);

        //assert
        $this->assertNull($response->headers->get('Access-Control-Allow-Origin'));
        $this->assertNull($response->headers->get('Access-Control-Allow-Methods'));
    }

    public function test_onResponse_no_cors()
    {
        //arrange
        $listener = new APICORSResponseListener();
        $event = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $request = new Request();
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::MASTER_REQUEST, KernelEvent::class);

        $response = new Response();
        $this->setObjectAttribute($event, 'response', $response, FilterResponseEvent::class);

        //act
        $listener->onResponse($event);

        //assert
        $this->assertNull($response->headers->get('Access-Control-Allow-Origin'));
        $this->assertNull($response->headers->get('Access-Control-Allow-Methods'));
    }

    public function test_onResponse_has_cors()
    {
        //arrange
        $listener = new APICORSResponseListener();
        $event = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $request = new Request();
        $request->attributes->set('_cors', array(
            'Access-Control-Allow-Origin' => 'http://localhost',
            'Access-Control-Allow-Methods' => 'PUT, GET, POST, DELETE, OPTIONS',
        ));
        $this->setObjectAttribute($event, 'request', $request, KernelEvent::class);
        $this->setObjectAttribute($event, 'requestType', HttpKernelInterface::MASTER_REQUEST, KernelEvent::class);

        $response = new Response();
        $this->setObjectAttribute($event, 'response', $response, FilterResponseEvent::class);

        //act
        $listener->onResponse($event);

        //assert
        $this->assertEquals('http://localhost', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertEquals('PUT, GET, POST, DELETE, OPTIONS', $response->headers->get('Access-Control-Allow-Methods'));
    }

    public function test_isValidOrigin_bad_origin_url()
    {
        //arrange
        $origin = 'bad_host';
        $allowOrigins = array(
            'localhost',
        );
        $listener = $this->getMockBuilder(APICORSResponseListener::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();
        $this->setObjectAttribute($listener, 'allowOrigins', $allowOrigins);


        //act
        $result = $this->callObjectMethod($listener, 'isValidOrigin', $origin);

        //assert
        $this->assertFalse($result);
    }

    public function test_isValidOrigin_null_domain()
    {
        //arrange
        $origin = 'http://localhost';
        $allowOrigins = array(
        );
        $listener = $this->getMockBuilder(APICORSResponseListener::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();
        $this->setObjectAttribute($listener, 'allowOrigins', $allowOrigins);


        //act
        $result = $this->callObjectMethod($listener, 'isValidOrigin', $origin);

        //assert
        $this->assertFalse($result);
    }

    public function test_isValidOrigin_not_in_valid_domain()
    {
        //arrange
        $origin = 'http://example.com';
        $allowOrigins = array(
            'localhost',
        );
        $listener = $this->getMockBuilder(APICORSResponseListener::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();
        $this->setObjectAttribute($listener, 'allowOrigins', $allowOrigins);


        //act
        $result = $this->callObjectMethod($listener, 'isValidOrigin', $origin);

        //assert
        $this->assertFalse($result);
    }

    public function test_isValidOrigin_valid()
    {
        //arrange
        $origin = 'http://example.com';
        $allowOrigins = array(
            'localhost',
            'example.com'
        );
        $listener = $this->getMockBuilder(APICORSResponseListener::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();
        $this->setObjectAttribute($listener, 'allowOrigins', $allowOrigins);

        //act
        $result = $this->callObjectMethod($listener, 'isValidOrigin', $origin);

        //assert
        $this->assertTrue($result);
    }

}