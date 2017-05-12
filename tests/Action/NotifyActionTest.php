<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Payum\Uniteller\Tests\Action;

use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Tests\GenericActionTest;
use Payum\Uniteller\Action\NotifyAction;
use Payum\Uniteller\Tests\ApiAwareTestTrait;
use Payum\Uniteller\Tests\GatewayAwareTestTrait;
use Payum\Uniteller\Tests\MocksTrait;
use Tmconsulting\Uniteller\Client;
use Tmconsulting\Uniteller\Signature\SignatureInterface;

class NotifyActionTest extends GenericActionTest
{
    use GatewayAwareTestTrait, ApiAwareTestTrait, MocksTrait;

    /**
     * @var Generic
     */
    protected $requestClass = Notify::class;

    /**
     * @var string
     */
    protected $actionClass = NotifyAction::class;


    public function testThrowIfSignatureInvalid()
    {
        $action = new NotifyAction();
        $action->setGateway($this->buildGetHttpRequestMock());
        $action->setApi($this->buildSignatureMock(false));

        try {
            $action->execute(new Notify([]));
        } catch (HttpResponse $e) {
            $this->assertEquals('Notification (callback) signature is invalid.', $e->getContent());
            $this->assertEquals(400, $e->getStatusCode());

            return;
        }

        $this->fail('The exception is expected');
    }

    public function testThrowSuccessfulResponse()
    {
        $action = new NotifyAction();
        $action->setGateway($this->buildGetHttpRequestMock());
        $action->setApi($this->buildSignatureMock(true));

        try {
            $action->execute(new Notify([]));
        } catch (HttpResponse $e) {
            $this->assertEquals('OK', $e->getContent());
            $this->assertEquals(200, $e->getStatusCode());

            return;
        }

        $this->fail('The exception is expected');
    }

    /**
     * @return \Payum\Core\GatewayInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildGetHttpRequestMock()
    {
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetHttpRequest::class))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->request = [
                    'Signature' => 'uniteller callback signature'
                ];
            }));

        return $gatewayMock;
    }

    /**
     * @param bool $return
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildSignatureMock($return = false)
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock
            ->expects($this->once())
            ->method('getSignature')
            ->willReturnCallback(function () use ($return) {
                $mock = $this->createMock(SignatureInterface::class);
                $mock->method('verify')->willReturn($return);

                return $mock;
            });

        return $clientMock;
    }
}