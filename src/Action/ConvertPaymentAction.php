<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 29/03/2017
 */

namespace Payum\Uniteller\Action;


class ConvertPaymentAction extends AbstractConvertPaymentAction
{
    /**
     * @param $payment
     * @param $request
     * @return mixed
     */
    protected function getDescription($payment, $request)
    {
        return $payment->getDescription();
    }
}