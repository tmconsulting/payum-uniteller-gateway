<?php
namespace Payum\Uniteller\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;
use Tmconsulting\Uniteller\Order\Status;

class StatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (null === $this->getOrderId($model) && null === $this->getParameter($model, 'Status')) {
            $request->markNew();

            return;
        }

        if ($this->getOrderId($model) && null === $this->getParameter($model, 'Status')) {
            $request->markPending();

            return;
        }

        switch (Status::resolve($this->getParameter($model, 'Status'))) {
            case Status::AUTHORIZED:
                $request->markAuthorized();
                break;
            case Status::NOT_AUTHORIZED:
                $request->markFailed();
                break;
            case Status::PAID:
                $request->markCaptured();
                break;
            case Status::CANCELLED:
                $request->markCanceled();
                break;
            case Status::WAITING:
                $request->markPending();
                break;
            default:
                $request->markUnknown();
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GetStatusInterface
          && $request->getModel() instanceof \ArrayAccess;
    }

    /**
     * @param $model
     * @param $key
     * @return mixed
     */
    protected function getParameter($model, $key)
    {
        if (array_key_exists($key, $model)) {
            return $model[$key];
        }

        return null;
    }

    /**
     * @param $model
     * @return mixed
     */
    protected function getOrderId($model)
    {
        foreach (['Order_ID', 'Order_IDP'] as $item) {
            if (array_key_exists($item, $model)) {
                return $model[$item];
            }
        }

        return null;
    }
}
