<?php
namespace Payum\Uniteller\Action\TokenExtraWorkaround;

use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetToken;
use Payum\Core\Request\Notify;
use Payum\Uniteller\Action\Api\BaseApiAwareAction;

class NotifyNullAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param Notify $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $this->gateway->execute($httpRequest = new GetHttpRequest);

        $orders = $this->api->results([
            'ShopOrderNumber' => $httpRequest->request['Order_ID']
        ]);

        /** @var \Tmconsulting\Uniteller\Order\Order $order */
        foreach ($orders as $order) {
            // get the payum token
            if (! $token = $order->getComment()) {
                throw new HttpResponse('The notification is invalid. Comment does not contain payum token.', 400);
            }

            $this->gateway->execute($getToken = new GetToken($token));
            $this->gateway->execute(new Notify($getToken->getToken()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            null === $request->getModel()
        ;
    }
}
