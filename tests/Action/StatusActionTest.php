<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Payum\Uniteller\Tests\Action;

use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Tests\GenericActionTest;
use Payum\Uniteller\Action\StatusAction;
use Payum\Uniteller\Tests\GatewayAwareTestTrait;
use Tmconsulting\Uniteller\Order\Status;

class StatusActionTest extends GenericActionTest
{
    use GatewayAwareTestTrait;

    /**
     * @var Generic
     */
    protected $requestClass = GetHumanStatus::class;

    /**
     * @var string
     */
    protected $actionClass = StatusAction::class;

    public function testShouldMarkNewIfOrderAndStatusIsNull()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([]));

        $this->assertTrue($human->isNew());
    }

    public function testShouldMarkPendingIfOrderExistsAndStatusIsNull()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus(['Order_ID' => 1]));

        $this->assertTrue($human->isPending());
    }

    public function testCanActionGetOrderId()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus(['Order_IDP' => 1]));
        $this->assertTrue($human->isPending());

        $action->execute($human = new GetHumanStatus(['Order_ID' => 1]));
        $this->assertTrue($human->isPending());
    }

    public function testShouldMarkAuthorizedIfGatewayStatusIsAuthorized()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => Status::AUTHORIZED
        ]));

        $this->assertTrue($human->isAuthorized());
    }

    public function testShouldMarkFailedIfGatewayStatusIsNotAuthorized()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => Status::NOT_AUTHORIZED
        ]));

        $this->assertTrue($human->isFailed());
    }

    public function testShouldMarkCapturedIfGatewayStatusIsPaid()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => Status::PAID
        ]));

        $this->assertTrue($human->isCaptured());
    }

    public function testShouldMarkCanceledIfGatewayStatusIsCancelled()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => Status::CANCELLED
        ]));

        $this->assertTrue($human->isCanceled());

        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => 'canceled'
        ]));

        $this->assertTrue($human->isCanceled());
    }

    public function testShouldMarkPendingIfGatewayStatusIsWaiting()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => Status::WAITING
        ]));

        $this->assertTrue($human->isPending());
    }

    public function testShouldMarkUnknownIfGatewayStatusIsWtf()
    {
        $action = new StatusAction();
        $action->execute($human = new GetHumanStatus([
            'Order_ID' => 1,
            'Status'   => 'wtf'
        ]));

        $this->assertTrue($human->isUnknown());
    }


}