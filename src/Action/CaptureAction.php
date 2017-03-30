<?php
namespace Payum\Uniteller\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Uniteller\Action\Api\BaseApiAwareAction;
use Tmconsulting\Uniteller\ClientInterface;

class CaptureAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (null === $model['URL_RETURN_OK'] && $request->getToken()) {
            $model['URL_RETURN_OK'] = $request->getToken()->getAfterUrl();
        }

        /** @var ClientInterface $client */
        $client = $this->api;
        $uri    = $client->payment($model->toUnsafeArray())->getUri();

        throw new HttpRedirect($uri);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
