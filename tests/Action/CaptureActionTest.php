<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Payum\Uniteller\Tests\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Capture;
use Payum\Uniteller\Action\CaptureAction;
use Payum\Uniteller\Tests\TestCase;
use Tmconsulting\Uniteller\Client;
use Tmconsulting\Uniteller\Payment\Uri;

class CaptureActionTest extends TestCase
{
    public function testWhenConstructedWithoutAnyArguments()
    {
        new CaptureAction();
    }

    public function testCaptureSupport()
    {
        $action  = new CaptureAction();
        $request = new Capture(new ArrayObject());

        $this->assertTrue($action->supports($request));
    }

    public function testSupportsApiClass()
    {
        $action = new CaptureAction();

        $action->setApi(new Client());
    }

    /**
     * @expectedException \Payum\Core\Exception\UnsupportedApiException
     */
    public function testExceptionThrowsIfNotSupportedApiClass()
    {
        $action = new CaptureAction();

        $action->setApi(new \stdClass());
    }

    /**
     * @expectedException \Payum\Core\Exception\RequestNotSupportedException
     */
    public function testExceptionThrowsIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $action = new CaptureAction();

        $action->execute(new \stdClass());
    }

    /**
     * @expectedException \Payum\Core\Reply\HttpRedirect
     */
    public function testHttpReplyExceptionThrowsWhenRequestExcecuted()
    {
        $model = new \ArrayObject([
            'Order_IDP'     => mt_rand(10000, 99999),
            'Subtotal_P'    => 10,
            'Customer_IDP'  => mt_rand(10000, 99999),
            'URL_RETURN_NO' => 'https://google.com/?q=failure',
        ]);

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->any())
            ->method('execute');

        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('payment')
            ->willReturn(new Uri('https://google.com/?q=url_generated'));

        $action  = new CaptureAction();
        $action->setGateway($gatewayMock);
        $action->setApi($clientMock);

        $action->execute(new Capture($model));
    }
}