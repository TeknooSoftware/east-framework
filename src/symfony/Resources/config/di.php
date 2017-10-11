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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\FoundationBundle\Resources\config;

use function DI\get;
use function DI\decorate;
use function DI\object;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\East\Foundation\Router\RouterInterface;
use Teknoo\East\FoundationBundle\Http\Client;
use Teknoo\East\FoundationBundle\Session\SessionMiddleware;

return [
    SessionMiddleware::class => object(SessionMiddleware::class),

    Client::class => get(ClientInterface::class),
    ClientInterface::class => object(Client::class)
        ->constructor(get(HttpFoundationFactory::class)),

    ManagerInterface::class => decorate(function ($previous, ContainerInterface $container) {
        if ($previous instanceof ManagerInterface) {
            $previous->registerMiddleware($container->get(SessionMiddleware::class, 5));
            $previous->registerMiddleware($container->get(RouterInterface::class, 10));
        }

        return $previous;
    })
];