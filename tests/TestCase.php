<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 15/03/2017
 */

namespace Payum\Uniteller\Tests;

use Payum\Core\GatewayInterface;
use PHPUnit_Framework_TestCase;
use Tmconsulting\Uniteller\ClientInterface;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Uniteller ClientInterface.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createClientMock()
    {
        return $this->createMock(ClientInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GatewayInterface
     */
    protected function createGatewayMock()
    {
        return $this->createMock(GatewayInterface::class);
    }
}