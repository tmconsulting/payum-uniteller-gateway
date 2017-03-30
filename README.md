# Payum Uniteller Gateway

## Install

`composer require tmconsulting/payum-uniteller@0.1.*`

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

## Resources

* [Documentation](https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md)
* [Questions](http://stackoverflow.com/questions/tagged/payum)
* [Issue Tracker](https://github.com/Payum/Payum/issues)
* [Twitter](https://twitter.com/payumphp)

## Tests

`composer test`

## License

Library is released under the [MIT License](LICENSE).
