<?php
namespace Payum\Uniteller\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Notify;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Sync;
use Tmconsulting\Uniteller\Client;
use Payum\Uniteller\Action\Api\BaseApiAwareAction;

class NotifyAction extends BaseApiAwareAction
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Notify $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest);

        /** @var Client $client */
        $client = $this->api;

        $signature = $httpRequest->request['Signature'];
        if (! $client->getSignature()->verify($signature, $httpRequest->request)) {
            // throw new HttpResponse('Notification (callback) signature is invalid.', 400);
        }

        $model->replace($httpRequest->request);

        throw new HttpResponse('OK');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
