<?php

declare(strict_types=1);

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

namespace Teknoo\East\Foundation\Recipe;

use Teknoo\East\Foundation\Middleware\MiddlewareInterface;
use Teknoo\Recipe\RecipeInterface as BaseInterface;

interface RecipeInterface extends BaseInterface
{
    /**
     * Method to register middleware in the manager to process request.
     *
     * @param MiddlewareInterface $middleware
     * @param int $priority
     * @param string $middlewareName=null
     *
     * @return RecipeInterface
     */
    public function registerMiddleware(
        MiddlewareInterface $middleware,
        int $priority = 10,
        string $middlewareName=null
    ): RecipeInterface;
}
