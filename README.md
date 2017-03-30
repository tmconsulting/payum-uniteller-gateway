# Payum Uniteller Gateway

<br />

<p align="center">
    <img src="https://www.uniteller.ru//local/templates/index/img/base/logo.svg" width="220" />
</p>

<br />

<p align="center">
    <img src="https://travis-ci.org/tmconsulting/payum-uniteller-gateway.svg?branch=master" />
    <img src="https://poser.pugx.org/tmconsulting/payum-uniteller-gateway/v/stable" />
    <img src="https://poser.pugx.org/tmconsulting/payum-uniteller-gateway/v/unstable" />
    <img src="https://poser.pugx.org/tmconsulting/payum-uniteller-gateway/license" />
    <img src="https://poser.pugx.org/tmconsulting/payum-uniteller-gateway/composerlock" />
</p>

<br />

Payum gateway package for Uniteller. Based on [uniteller-php-sdk](https://github.com/tmconsulting/uniteller-php-sdk).

## Install

`composer require tmconsulting/payum-uniteller`

After composer installation, add some gateway to `PayumBuilder`:

```php
use Payum\Core\GatewayFactoryInterface;
$builder->addGatewayFactory('uniteller', function(array $config, GatewayFactoryInterface $coreGatewayFactory) {
    return new \Payum\Uniteller\UnitellerGatewayFactory($config, $coreGatewayFactory);
})
->addGateway('uniteller', [
    'factory'      => 'uniteller',
    'token_extra'  => false, // enable this options, if you want to set token to comment field.
    'shop_id'      => 'shop_od for production',
    'test_shop_id' => 'shop_id for sandbox',
    'login'        => 'login_digits',
    'password'     => 'password',
    'sandbox'      => true,
]);
```

Since Uniteller does not supports callback urls with dynamic parameters. So, you will should implement `notify` action:

```php
use Payum\Core\Request\Notify;
use Payum\Core\Request\GetHumanStatus;

class PaymentController extends PayumController
{
    public function notifyAction(Request $request)
    {
        $gateway = $this->getPayum()->getPayment('uniteller');
        $payment = $this->getPayum()
            ->getStorage(Payment::class)
            ->findBy([
                // find payum token by Order_ID, when uniteller call you callback url
                'number' => $request->get('Order_ID'),
            ]);

        if ($reply = $gateway->execute(new Notify($payment), true)) {
            if ($reply instanceof HttpResponse) {
                $gateway->execute($status = new GetHumanStatus($payment));

                if ($status->isCaptured() || $status->isAuthorized()) {
                    // Payment is done
                    // Notify your app here
                    // Payum library does not update status in the database
                }

                throw $reply;
            }

            throw new \LogicException('Unsupported reply', null, $reply);
        }

        return new Response('', 204);
    }
}
```

... or if you're disable comment field in admin panel, you can use "token extra workaround". Just enable `token_extra` option.

## Resources

* [Documentation](https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md)
* [Questions](http://stackoverflow.com/questions/tagged/payum)
* [Issue Tracker](https://github.com/Payum/Payum/issues)
* [Twitter](https://twitter.com/payumphp)

## Old versions

[@fullpipe](https://github.com/fullpipe) implement similar package for payum 0.14.* <br>
[You can use it](https://github.com/fullpipe/payum-uniteller).

## Tests

`composer test`

## License

Library is released under the [MIT License](LICENSE).
