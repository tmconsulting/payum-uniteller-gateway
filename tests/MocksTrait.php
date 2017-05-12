<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 11/05/2017
 */

namespace Payum\Uniteller\Tests;


use Payum\Core\GatewayInterface;
use Tmconsulting\Uniteller\ClientInterface;

trait MocksTrait
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