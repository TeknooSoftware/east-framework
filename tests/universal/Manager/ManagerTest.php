<?php
/**
 * East Foundation.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\East\Foundation\Manager;

use Psr\Http\Message\ServerRequestInterface;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\Foundation\Manager\Manager;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\East\Foundation\Router\RouterInterface;

/**
 * Class ManagerTest
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @covers \Teknoo\East\Foundation\Manager\Manager
 * @covers \Teknoo\East\Foundation\Manager\States\HadRun
 * @covers \Teknoo\East\Foundation\Manager\States\Running
 * @covers \Teknoo\East\Foundation\Manager\States\Service
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Manager
     */
    private function buildManager(): Manager
    {
        return new Manager();
    }

    /**
     * @return string
     */
    public function getManagerClass(): string
    {
        return Manager::class;
    }

    public function testReceiveRequestFromClient()
    {
        $this->assertInstanceOf(
            $this->getManagerClass(),
            $this->buildManager()->receiveRequestFromClient(
                $this->createMock(ClientInterface::class),
                $this->createMock(ServerRequestInterface::class)
            )
        );
    }

    /**
     * @expectedException \TypeError
     */
    public function testReceiveRequestFromClientErrorClient()
    {
        $this->buildManager()->receiveRequestFromClient(
            new \stdClass(),
            $this->createMock(ServerRequestInterface::class)
        );
    }

    /**
     * @expectedException \TypeError
     */
    public function testReceiveRequestFromClientErrorRequest()
    {
        $this->buildManager()->receiveRequestFromClient(
            $this->createMock(ClientInterface::class),
            new \stdClass()
        );
    }

    public function testRegisterRouter()
    {
        $this->assertInstanceOf(
            $this->getManagerClass(),
            $this->buildManager()->registerRouter(
                $this->createMock(RouterInterface::class)
            )
        );
    }

    /**
     * @expectedException \TypeError
     */
    public function testRegisterRouterError()
    {
        $this->buildManager()->registerRouter(new \stdClass());
    }

    public function testUnregisterRouter()
    {
        $router = $this->createMock(RouterInterface::class);
        $this->assertInstanceOf(
            $this->getManagerClass(),
            $this->buildManager()->unregisterRouter(
                $router
            )
        );

        $router = $this->createMock(RouterInterface::class);
        $this->assertInstanceOf(
            $this->getManagerClass(),
            $this->buildManager()->registerRouter($router)->unregisterRouter($router)
        );
    }

    /**
     * @expectedException \TypeError
     */
    public function testUnregisterRouterError()
    {
        $this->buildManager()->unregisterRouter(new \stdClass());
    }

    public function testStopPropagation()
    {
        $router = new class($this) implements RouterInterface {
            /**
             * @var ManagerTest
             */
            private $testSuite;

            public function __construct(ManagerTest $that)
            {
                $this->testSuite = $that;
            }

            public function receiveRequestFromServer(
                ClientInterface $client,
                ServerRequestInterface $request,
                ManagerInterface $manager
            ): RouterInterface
            {
                $this->testSuite->assertInstanceOf(
                    $this->testSuite->getManagerClass(),
                    $manager->stopPropagation()
                );

                return $this;
            }
        };


        /**
         * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject $clientMock
         */
        $clientMock = $this->createMock(ClientInterface::class);

        /**
         * @var ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject $serverRequestMock
         */
        $serverRequestMock = $this->createMock(ServerRequestInterface::class);

        $manager = $this->buildManager();
        $manager->registerRouter($router);
        $manager->receiveRequestFromClient($clientMock,$serverRequestMock);
    }

    public function testBehaviorReceiveRequestFromClient()
    {
        $manager = $this->buildManager();

        /**
         * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject $clientMock
         */
        $clientMock = $this->createMock(ClientInterface::class);

        /**
         * @var ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject $serverRequestMock
         */
        $serverRequestMock = $this->createMock(ServerRequestInterface::class);

        /**
         * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject $router1
         */
        $router1 = $this->createMock(RouterInterface::class);
        $router1->expects($this->once())->method('receiveRequestFromServer')->willReturnSelf();
        /**
         * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject $router1
         */
        $router2 = $this->createMock(RouterInterface::class);
        $router2->expects($this->once())->method('receiveRequestFromServer');
        $router2->expects($this->once())->method('receiveRequestFromServer')->willReturnCallback(
            function ($clientPassed, $requestPassed, $managerPassed) use ($clientMock, $serverRequestMock, $manager) {
                $this->assertEquals($clientPassed, $clientMock);
                $this->assertEquals($requestPassed, $serverRequestMock);
                $this->assertNotSame($managerPassed, $manager);
                $managerPassed->stopPropagation();
            }
        );

        /**
         * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject $router1
         */
        $router3 = $this->createMock(RouterInterface::class);
        $router3->expects($this->never())->method('receiveRequestFromServer');

        $manager->registerRouter($router1);
        $manager->registerRouter($router2);
        $manager->registerRouter($router3);
        $this->assertInstanceOf(
            $this->getManagerClass(),
            $manager->receiveRequestFromClient($clientMock,$serverRequestMock)
        );
    }
}