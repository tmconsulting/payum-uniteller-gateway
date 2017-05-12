<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Payum\Uniteller\Tests\Action;

use Payum\Core\Model\Payment;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\Generic;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Tests\GenericActionTest;
use Payum\Uniteller\Action\ConvertPaymentAction;
use Payum\Uniteller\Tests\GatewayAwareTestTrait;
use Payum\Uniteller\Tests\MocksTrait;

class ConvertPaymentActionTest extends GenericActionTest
{
    use GatewayAwareTestTrait, MocksTrait;

    /**
     * @var Generic
     */
    protected $requestClass = Convert::class;

    /**
     * @var string
     */
    protected $actionClass = ConvertPaymentAction::class;

    /**
     * @return array
     */
    public function provideSupportedRequests()
    {
        return [
            [new $this->requestClass(new Payment(), 'array')],
            [new $this->requestClass($this->createMock(PaymentInterface::class), 'array')],
            [new $this->requestClass(new Payment(), 'array', $this->createMock(TokenInterface::class))],
        ];
    }

    /**
     * @return array
     */
    public function provideNotSupportedRequests()
    {
        return [
            ['foo'],
            [['foo']],
            [new \stdClass()],
            [$this->getMockForAbstractClass(Generic::class, [[]])],
            [new $this->requestClass(new \stdClass(), 'array')],
            [new $this->requestClass(new Payment(), 'foobar')],
            [new $this->requestClass($this->createMock(PaymentInterface::class), 'foobar')],
        ];
    }

    public function testShouldCorrectlyConvertOrderToDetailsAndSetItBack()
    {
        $payment = new Payment();
        $payment->setNumber('theNumber');
        $payment->setCurrencyCode('USD');
        $payment->setTotalAmount(123);
        $payment->setDescription('the description');
        $payment->setClientId('theClientId');
        $payment->setClientEmail('theClientEmail');

        $action = new ConvertPaymentAction();
        $action->execute($convert = new Convert($payment, 'array'));

        $details = $convert->getResult();
        $this->assertNotEmpty($details);

        $this->assertArrayHasKey('Order_IDP', $details);
        $this->assertEquals('theNumber', $details['Order_IDP']);

        $this->assertArrayHasKey('Subtotal_P', $details);
        $this->assertEquals(123 / 100, $details['Subtotal_P']);

        $this->assertArrayHasKey('Currency', $details);
        $this->assertEquals('USD', $details['Currency']);

        $this->assertArrayHasKey('Customer_IDP', $details);
        $this->assertEquals('theClientId', $details['Customer_IDP']);

        $this->assertArrayHasKey('Email', $details);
        $this->assertEquals('theClientEmail', $details['Email']);

        $this->assertArrayHasKey('Comment', $details);
        $this->assertEquals('the description', $details['Comment']);
    }

    public function testFillHashToCommentFieldIfUsedTokenExtraApproach()
    {
        $payment = new Payment();
        $payment->setNumber('theNumber');
        $payment->setTotalAmount(123);
        $payment->setCurrencyCode('USD');


        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock
            ->method('getHash')
            ->willReturn('hash');

        $action = new \Payum\Uniteller\Action\TokenExtraWorkaround\ConvertPaymentAction();
        $action->execute($convert = new Convert($payment, 'array', $tokenMock));

        $comment = $convert->getResult()['Comment'];

        $this->assertEquals('hash', $comment);
        $this->assertEquals($convert->getToken()->getHash(), $comment);
    }
}