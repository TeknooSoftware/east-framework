<?php
/**
 * East Foundation.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\East\Twig\Template;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Teknoo\East\Foundation\Promise\PromiseInterface;
use Teknoo\East\Foundation\Template\ResultInterface;
use Teknoo\East\Foundation\Template\EngineInterface;
use Teknoo\East\Twig\Template\Engine;
use Twig\Environment;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @covers \Teknoo\East\Twig\Template\Engine
 */
class EngineTest extends TestCase
{
    private ?Environment $twig = null;

    /**
     * @return Environment|MockObject
     */
    public function getTwig(): Environment
    {
        if (!$this->twig instanceof Environment) {
            $this->twig = $this->createMock(Environment::class);
        }

        return $this->twig;
    }

    public function buildEngine(): Engine
    {
        return new Engine($this->getTwig());
    }

    public function testRenderNotCallResult()
    {
        $view = 'foo';
        $parameters = ['foo' => 'bar'];

        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects(self::once())
            ->method('success')
            ->with(self::callback(fn($r) => $r instanceof ResultInterface));

        self::assertInstanceOf(
            EngineInterface::class,
            $this->buildEngine()->render(
                $promise,
                $view,
                $parameters
            )
        );
    }

    public function testRenderCallingResult()
    {
        $view = 'foo';
        $parameters = ['foo' => 'bar'];

        $this->getTwig()
            ->expects(self::once())
            ->method('render')
            ->with($view, $parameters)
            ->willReturn('bar');

        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects(self::once())
            ->method('success')
            ->willReturnCallback(
                function (ResultInterface $result) use ($promise) {
                    self::assertEquals('bar', (string) $result);

                    return $promise;
                }
            );

        self::assertInstanceOf(
            EngineInterface::class,
            $this->buildEngine()->render(
                $promise,
                $view,
                $parameters
            )
        );
    }

    public function testRenderError()
    {
        $view = 'foo';
        $parameters = ['foo' => 'bar'];

        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects(self::once())
            ->method('success')
            ->willThrowException(new \RuntimeException('foo'));
        $promise->expects(self::once())->method('fail');

        self::assertInstanceOf(
            EngineInterface::class,
            $this->buildEngine()->render(
                $promise,
                $view,
                $parameters
            )
        );
    }
}
