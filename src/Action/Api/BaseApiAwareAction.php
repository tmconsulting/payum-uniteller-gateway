<?php
namespace Payum\Uniteller\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Uniteller\Api;
use Tmconsulting\Uniteller\Client as UnitellerClient;
use Tmconsulting\Uniteller\ClientInterface;

abstract class BaseApiAwareAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use GatewayAwareTrait;
    use ApiAwareTrait;

    /**
     * BaseApiAwareAction constructor.
     */
    public function __construct()
    {
        $this->apiClass = ClientInterface::class;
    }
}
