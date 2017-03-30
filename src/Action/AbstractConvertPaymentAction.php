<?php
namespace Payum\Uniteller\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

abstract class AbstractConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param $payment
     * @param $request
     * @return mixed
     */
    abstract protected function getDescription($payment, $request);

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());
        $details['Order_IDP']    = $payment->getNumber();
        $details['Subtotal_P']   = ((float) $payment->getTotalAmount()) / 100;
        $details['Currency']     = $payment->getCurrencyCode();
        $details['Customer_IDP'] = $payment->getClientId();
        $details['Email']        = $payment->getClientEmail();
        $details['Comment']      = $this->getDescription($payment, $request);

        $details->validateNotEmpty([
            'Order_IDP',
            'Subtotal_P',
            'Currency',
        ]);

        $request->setResult((array) $details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array'
        ;
    }
}
