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

namespace Teknoo\Tests\East\FoundationBundle\Messenger;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Teknoo\East\Foundation\Client\ClientInterface;
use Teknoo\East\Foundation\Manager\Manager;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\East\FoundationBundle\Messenger\Executor;
use Teknoo\Recipe\BaseRecipeInterface;
use Teknoo\Recipe\Bowl\BowlInterface;
use Teknoo\Recipe\ChefInterface;
use Teknoo\Recipe\RecipeInterface;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @covers \Teknoo\East\FoundationBundle\Messenger\Executor
 */
class ExecutorTest extends TestCase
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @return ManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getManagerMock()
    {
        if (!$this->manager instanceof ManagerInterface) {
            $this->manager = $this->createMock(ManagerInterface::class);
        }

        return $this->manager;
    }

    private function buildExecutor(): Executor
    {
        return new Executor(
            $this->getManagerMock()
        );
    }

    public function testExecuteBadRecipe()
    {
        $this->expectException(\TypeError::class);
        $this->buildExecutor()->execute(
            new \stdClass(),
            $this->createMock(MessageInterface::class),
            $this->createMock(ClientInterface::class),
            [],
        );
    }

    public function testExecuteBadWorkPlan()
    {
        $this->expectException(\TypeError::class);
        $this->buildExecutor()->execute(
            $this->createMock(BaseRecipeInterface::class),
            $this->createMock(MessageInterface::class),
            $this->createMock(ClientInterface::class),
            new \stdClass(),
        );
    }

    public function testExecuteBadMessage()
    {
        $this->expectException(\TypeError::class);
        $this->buildExecutor()->execute(
            $this->createMock(BaseRecipeInterface::class),
            new \stdClass(),
            $this->createMock(ClientInterface::class),
            [],
        );
    }

    public function testExecuteBadClient()
    {
        $this->expectException(\TypeError::class);
        $this->buildExecutor()->execute(
            $this->createMock(BaseRecipeInterface::class),
            $this->createMock(ClientInterface::class),
            new \stdClass(),
            [],
        );
    }

    public function testExecute()
    {
        $executor = $this->buildExecutor();
        self::assertInstanceOf(
            Executor::class,
            $executor->execute(
                $this->createMock(BaseRecipeInterface::class),
                $this->createMock(MessageInterface::class),
                $this->createMock(ClientInterface::class),
                []
            )
        );
    }

    public function testExecuteTwoTimes()
    {
        $executor = new Executor(new Manager());
        $recipe = $this->createMock(RecipeInterface::class);
        $recipe->expects(self::any())->method('train')->willReturnCallback(
            function (ChefInterface $chef) use ($recipe) {
                $chef->followSteps([$this->createMock(BowlInterface::class)]);

                return $recipe;
            }
        );

        self::assertInstanceOf(
            Executor::class,
            $executor->execute(
                $recipe,
                $this->createMock(MessageInterface::class),
                $this->createMock(ClientInterface::class),
                []
            )
        );

        self::assertInstanceOf(
            Executor::class,
            $executor->execute(
                $recipe,
                $this->createMock(MessageInterface::class),
                $this->createMock(ClientInterface::class),
                []
            )
        );
    }
}
