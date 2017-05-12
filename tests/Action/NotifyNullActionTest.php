<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Payum\Uniteller\Tests\Action;

use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetToken;
use Payum\Core\Request\Notify;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Tests\GenericActionTest;
use Payum\Uniteller\Action\TokenExtraWorkaround\NotifyNullAction;
use Payum\Uniteller\Tests\ApiAwareTestTrait;
use Payum\Uniteller\Tests\GatewayAwareTestTrait;
use Payum\Uniteller\Tests\MocksTrait;
use Tmconsulting\Uniteller\Order\Order;

class NotifyNullActionTest extends GenericActionTest
{
    use GatewayAwareTestTrait, ApiAwareTestTrait, MocksTrait;

    /**
     * @var Generic
     */
    protected $requestClass = Notify::class;

    /**
     * @var string
     */
    protected $actionClass = NotifyNullAction::class;

    public function provideSupportedRequests()
    {
        return [
            [new $this->requestClass(null)],
        ];
    }

    public function testThrowExceptionIfPayumTokenNotExists()
    {
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetHttpRequest::class))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->request = [
                    'Order_ID' => -1
                ];
            }));

        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('results')
            ->with($this->isType('array'))
            ->willReturn([new Order()]);

        $action = new NotifyNullAction();
        $action->setGateway($gatewayMock);
        $action->setApi($clientMock);

        try {
            $action->execute(new Notify(null));
        } catch (HttpResponse $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('The notification is invalid. Comment does not contain payum token.', $e->getContent());

            return;
        }

        $this->fail('The exception is expected');
    }

    public function testShouldBeCallNotifyActionAfterReceivePayumToken()
    {
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->at(0))
            ->method('execute')
            ->with($this->isInstanceOf(GetHttpRequest::class))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->request = [
                    'Order_ID' => -1
                ];
            }));

        $gatewayMock
            ->expects($this->at(1))
            ->method('execute')
            ->with($this->isInstanceOf(GetToken::class))
            ->will($this->returnCallback(function (GetToken $getToken) {
                $getToken->setToken($this->createMock(TokenInterface::class));
            }));

        $gatewayMock
            ->expects($this->at(2))
            ->method('execute')
            ->with($this->isInstanceOf(Notify::class));

        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('results')
            ->with($this->isType('array'))
            ->willReturnCallback(function() {
                $order = new Order();
                $order->setComment('hash');

                return [
                    $order
                ];
            });

        $action = new NotifyNullAction();
        $action->setGateway($gatewayMock);
        $action->setApi($clientMock);

        $reply = $action->execute(new Notify(null), true);

        $this->assertNull($reply);
    }
}