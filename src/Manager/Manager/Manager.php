<?php
/**
 * East Framework.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\Framework\Manager\Manager;

use Teknoo\East\Framework\Http\ClientInterface;
use Teknoo\East\Framework\Manager\ManagerInterface;
use Teknoo\East\Framework\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teknoo\Immutable\ImmutableInterface;
use Teknoo\Immutable\ImmutableTrait;
use Teknoo\States\Proxy\IntegratedInterface;
use Teknoo\States\Proxy\IntegratedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

/**
 * Class Manager to process requests in East Framework. The manager
 * passes the request to each router as the spread has not been stopped.
 *
 * All public method of the manager must only return the self client or a clone instance.
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Manager implements
    ManagerInterface,
    ImmutableInterface,
    ProxyInterface,
    IntegratedInterface
{
    use ImmutableTrait,
        ProxyTrait,
        IntegratedTrait {
        ProxyTrait::__set insteadof ImmutableTrait;
        ImmutableTrait::__unset insteadof ProxyTrait; //Disabling __unset() from States
    }

    /**
     * Class name of the factory to use in set up to initialize this object in this construction.
     *
     * @var string
     */
    protected static $startupFactoryClassName = '\Teknoo\States\Factory\StandardStartupFactory';

    /**
     * @var RouterInterface[]
     */
    private $routersList;

    /**
     * @var boolean
     */
    private $doRequestPropagation = false;

    /**
     * Manager constructor.
     * Initialize States behavior and Immutable behavior
     */
    public function __construct()
    {
        //Use ArrayObject instead of array type
        $this->routersList = new \ArrayObject();
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Call the startup factory to initialize this proxy
        $this->initializeObjectWithFactory();
        //Behavior for Immutable
        $this->uniqueConstructorCheck();
        //Enable the main state "Service"
        $this->enableState('Service');
    }

    /**
     * {@inheritdoc}
     */
    public function receiveRequestFromClient(ClientInterface $client, ServerRequestInterface $request): ManagerInterface
    {
        //Run this request
        return $this->running($client, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function registerRouter(RouterInterface $router): ManagerInterface
    {
        return $this->doRegisterRouter($router);
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterRouter(RouterInterface $router): ManagerInterface
    {
        return $this->doUnregisterRouter($router);
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation(): ManagerInterface
    {
        return $this->doStopPropagation();
    }
}
