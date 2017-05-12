<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 12/05/2017
 */

namespace Payum\Uniteller\Tests;

use Payum\Core\ApiAwareInterface;

trait ApiAwareTestTrait
{
    public function testShouldImplementApiAwareInterface()
    {
        $rc = new \ReflectionClass($this->actionClass);
        $this->assertTrue($rc->implementsInterface(ApiAwareInterface::class));
    }

    /**
     * @expectedException \Payum\Core\Exception\UnsupportedApiException
     */
    public function testCanActionThrowIfUnsupportedApiGiven()
    {
        $action = new $this->actionClass();
        $action->setApi(new \stdClass());
    }
}