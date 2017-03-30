<?php
namespace Payum\Uniteller\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Cancel;
use Payum\Uniteller\Action\Api\BaseApiAwareAction;

// use Tmconsulting\Uniteller\ClientInterface;

class CancelAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param Cancel $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

//        /** @var ClientInterface $client */
//        $client = $this->api;
//        /** @var array $orders */
//        $orders = $client->cancel((array) $model);
        

//        $this->gateway->execute(new Sync($request->getModel()));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Cancel &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
