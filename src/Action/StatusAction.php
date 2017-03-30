<?php
namespace Payum\Uniteller\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHumanStatus;
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

        file_put_contents('test.json', json_encode($model, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), FILE_APPEND);

        if (null === $model['Order_IDP'] && null === $model['Status']) {
            $request->markNew();

            return;
        }

        if ($model['Order_IDP'] && null === $model['Status']) {
            $request->markPending();

            return;
        }

        switch (Status::resolve($model['Status'])) {
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
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
