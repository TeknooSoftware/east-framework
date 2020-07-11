<?php

/*
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
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\East\FoundationBundle\Twig;

use Teknoo\East\Foundation\Promise\PromiseInterface;
use Teknoo\East\Foundation\Template\EngineInterface;
use Teknoo\East\Foundation\Template\ResultInterface;
use Twig\Environment;

class Engine implements EngineInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(PromiseInterface $promise, string $view, array $parameters = []): EngineInterface
    {
        $promise->success(
            new class($this->twig, $view, $parameters) implements ResultInterface {
                private Environment $twig;

                private string $view;

                private array $parameters;

                public function __construct(Environment $twig, string $view, array $parameters)
                {
                    $this->twig = $twig;
                    $this->view = $view;
                    $this->parameters = $parameters;
                }

                public function __toString(): string
                {
                    return $this->twig->render($this->view, $this->parameters);
                }
            }
        );

        return $this;
    }
}