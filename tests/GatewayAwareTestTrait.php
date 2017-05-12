<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 12/05/2017
 */

namespace Payum\Uniteller\Tests;

use Payum\Core\GatewayAwareInterface;

trait GatewayAwareTestTrait
{
    public function testShouldImplementGatewayAwareInterface()
    {
        $rc = new \ReflectionClass($this->actionClass);
        $this->assertTrue($rc->implementsInterface(GatewayAwareInterface::class));
    }
}