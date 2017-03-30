<?php
namespace Payum\Uniteller;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Payum\Uniteller\Action\CaptureAction;
use Payum\Uniteller\Action\ConvertPaymentAction;
use Payum\Uniteller\Action\NotifyAction;
use Payum\Uniteller\Action\RefundAction;
use Payum\Uniteller\Action\StatusAction;
use Payum\Uniteller\Action\TokenExtraWorkaround\NotifyNullAction;
use Payum\Uniteller\Action\TokenExtraWorkaround\ConvertPaymentAction as WorkaroundConvertPaymentAction;
use Tmconsulting\Uniteller\Client;

class UnitellerGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name'   => 'uniteller',
            'payum.factory_title'  => 'Uniteller Processing',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status'  => new StatusAction(),
            'payum.action.notify'  => new NotifyAction()
        ]);

        if ($config['payum.api']) {
            return;
        }

        $config['payum.default_options'] = [
            'shop_id'      => '',
            'test_shop_id' => '',
            'token_extra'  => false,
            'login'        => '',
            'password'     => '',
            'base_uri'     => 'https://wpay.uniteller.ru',
            'sandbox'      => true,
        ];
        $config->defaults($config['payum.default_options']);
        $config['payum.required_options'] = ['shop_id', 'login', 'password'];

        $this->useCommentFieldForToken($config);
        $config['payum.api'] = function (ArrayObject $config) {
            $config->validateNotEmpty($config['payum.required_options']);
            $shopId = $config['sandbox'] ? $config['test_shop_id'] : $config['shop_id'];

            $uniteller = new Client();
            $uniteller->setShopId($shopId);
            $uniteller->setLogin($config['login']);
            $uniteller->setPassword($config['password']);
            $uniteller->setBaseUri($config['base_uri']);

            return $uniteller;
        };
    }

    /**
     * Use [comment] field for payum token or not.
     * NOTICE: PLEASE HIDE COMMENT FIELD IN ADMIN WEB PANEL.
     *
     * @param \Payum\Core\Bridge\Spl\ArrayObject $config
     */
    protected function useCommentFieldForToken(ArrayObject $config)
    {
        if ((bool) $config['token_extra']) {
            $config['payum.action.notify_null']     = new NotifyNullAction();
            $config['payum.action.convert_payment'] = new WorkaroundConvertPaymentAction();
        } else {
            $config['payum.action.convert_payment'] = new ConvertPaymentAction();
        }
    }
}
