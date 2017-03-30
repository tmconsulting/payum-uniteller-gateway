<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Payum\Uniteller\Tests;

use Payum\Core\CoreGatewayFactory;
use Payum\Core\Gateway;
use Payum\Core\GatewayFactory;
use Payum\Uniteller\UnitellerGatewayFactory;
use ReflectionClass;

class UnitellerGatewayFactoryTest extends TestCase
{
    public function testWhenConstructedWithoutAnyArguments()
    {
        new UnitellerGatewayFactory();
    }

    public function testShouldSubClassGatewayFactory()
    {
        $reflection = new ReflectionClass(UnitellerGatewayFactory::class);
        $this->assertTrue($reflection->isSubclassOf(GatewayFactory::class));
    }

    public function testCreatesWithoutCoreGatewayFactory()
    {
        $factory = new UnitellerGatewayFactory();
        $this->assertAttributeInstanceOf(CoreGatewayFactory::class, 'coreGatewayFactory', $factory);
    }

    public function testIfConfigurationContainDefaultOptions()
    {
        $factory = new UnitellerGatewayFactory();
        $config  = $factory->createConfig();

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('payum.default_options', $config);

        $defaults = [
            'shop_id'      => '',
            'test_shop_id' => '',
            'token_extra'  => false,
            'login'        => '',
            'password'     => '',
            'base_uri'     => 'https://wpay.uniteller.ru',
            'sandbox'      => true,
        ];

        $this->assertEquals($defaults, $config['payum.default_options']);
    }

    public function testPayumConfigurationContainFactoryNameAndTitle()
    {
        $factory = new UnitellerGatewayFactory();
        $config  = $factory->createConfig();

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('payum.factory_name', $config);
        $this->assertEquals('uniteller', $config['payum.factory_name']);

        $this->assertArrayHasKey('payum.factory_title', $config);
        $this->assertEquals('Uniteller Processing', $config['payum.factory_title']);
    }

    public function testAllowCreateGatewayWithCustomApi()
    {
        $factory = new UnitellerGatewayFactory();
        $gateway = $factory->create(['payum.api' => new \stdClass()]);

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertAttributeNotEmpty('apis', $gateway);
        $this->assertAttributeNotEmpty('actions', $gateway);
        $extensions = $this->readAttribute($gateway, 'extensions');
        $this->assertAttributeNotEmpty('extensions', $extensions);
    }

    public function testGatewayCreating()
    {
        $factory = new UnitellerGatewayFactory();
        $gateway = $factory->create([
            'shop_id'      => 'boo',
            'test_shop_id' => 'baz',
            'token_extra'  => true,
            'login'        => 'bar',
            'password'     => 'foo',
            'base_uri'     => 'https://wpay.uniteller.ru',
            'sandbox'      => true,
        ]);

        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertAttributeNotEmpty('apis', $gateway);
        $this->assertAttributeNotEmpty('actions', $gateway);

        $extensions = $this->readAttribute($gateway, 'extensions');
        $this->assertAttributeNotEmpty('extensions', $extensions);
    }
}