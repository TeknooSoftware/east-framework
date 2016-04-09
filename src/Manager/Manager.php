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

namespace Teknoo\East\Framework\Manager;

use Teknoo\East\Framework\Http\ClientInterface;
use Teknoo\East\Framework\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teknoo\Immutable\ImmutableInterface;
use Teknoo\Immutable\ImmutableTrait;

/**
 * Class Manager
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Manager implements ManagerInterface, ImmutableInterface
{
    use ImmutableTrait;

    /**
     * @var RouterInterface[]
     */
    protected $routersList = [];

    /**
     * @var boolean
     */
    protected $doRequestPropagation;

    /**
     * Build a Generator to stop the list at reception of the stop message
     * @return \Generator
     */
    private function iterateRouter()
    {
        foreach ($this->routersList as $router) {
            yield $router;

            if (false === $this->doRequestPropagation) {
                break;
            }
        }
    }

    /**
     * To dispatch the request to all routers while a message was not receive to stop the propaggation
     * @param ClientInterface $client
     * @param ServerRequestInterface $request
     * @return ManagerInterface
     */
    private function dispatchRequest(ClientInterface $client, ServerRequestInterface $request): ManagerInterface
    {
        $this->doRequestPropagation = true;

        /**
         * @var RouterInterface $router
         */
        foreach ($this->iterateRouter() as $router) {
            $router->receiveRequestFromServer($client, $request, $this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function receiveRequestFromClient(ClientInterface $client, ServerRequestInterface $request): ManagerInterface
    {
        $manager = clone $this;
        $manager->dispatchRequest($client, $request);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerRouter(RouterInterface $router): ManagerInterface
    {
        $this->routersList[spl_object_hash($router)] = $router;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterRouter(RouterInterface $router): ManagerInterface
    {
        $routerHash = spl_object_hash($router);
        if (isset($this->routersList[$routerHash])) {
            unset($this->routersList[$routerHash]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation(): ManagerInterface
    {
        $this->doRequestPropagation = false;

        return $this;
    }
}