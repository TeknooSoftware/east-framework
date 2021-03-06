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

namespace Teknoo\Tests\East\Foundation;

use DI\Container;
use Psr\Log\LoggerInterface;
use Teknoo\East\Foundation\Manager\Manager;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\East\Foundation\Manager\Queue\Queue;
use Teknoo\East\Foundation\Manager\Queue\QueueInterface;
use Teknoo\East\Foundation\Processor\LoopDetector;
use Teknoo\East\Foundation\Processor\LoopDetectorInterface;
use Teknoo\East\Foundation\Processor\Processor;
use Teknoo\East\Foundation\Processor\ProcessorCookbook;
use Teknoo\East\Foundation\Processor\ProcessorCookbookInterface;
use Teknoo\East\Foundation\Processor\ProcessorInterface;
use Teknoo\East\Foundation\Processor\ProcessorRecipeInterface;
use Teknoo\East\Foundation\Recipe\Recipe;
use Teknoo\East\Foundation\Recipe\Cookbook;
use Teknoo\East\Foundation\Recipe\CookbookInterface;
use Teknoo\East\Foundation\Recipe\RecipeInterface;
use Teknoo\East\Foundation\Router\RouterInterface;

/**
 * Class DefinitionProviderTest.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/east Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return Container
     */
    protected function buildContainer() : Container
    {
        return include __DIR__ . '/../../src/generator.php';
    }

    public function testCreateManager()
    {
        $container = $this->buildContainer();
        $container->set(LoggerInterface::class, $this->createMock(LoggerInterface::class));
        $container->set(RouterInterface::class, $this->createMock(RouterInterface::class));
        $manager1 = $container->get(Manager::class);
        $manager2 = $container->get(ManagerInterface::class);

        self::assertInstanceOf(
            Manager::class,
            $manager1
        );

        self::assertInstanceOf(
            Manager::class,
            $manager2
        );

        self::assertSame($manager1, $manager2);
    }

    public function testCreateProcessor()
    {
        $container = $this->buildContainer();
        $container->set(LoggerInterface::class, $this->createMock(LoggerInterface::class));
        $manager = $this->createMock(ManagerInterface::class);
        $container->set(ManagerInterface::class, $manager);
        $processor1 = $container->get(ProcessorInterface::class);
        $processor2 = $container->get(Processor::class);

        self::assertInstanceOf(
            Processor::class,
            $processor1
        );

        self::assertInstanceOf(
            Processor::class,
            $processor2
        );

        self::assertSame($processor1, $processor2);
    }

    public function testLoopDetector()
    {
        $container = $this->buildContainer();
        $loopDetector1 = $container->get(LoopDetectorInterface::class);
        $loopDetector2 = $container->get(LoopDetector::class);

        self::assertInstanceOf(
            LoopDetector::class,
            $loopDetector1
        );

        self::assertInstanceOf(
            LoopDetector::class,
            $loopDetector2
        );

        self::assertSame($loopDetector1, $loopDetector2);
    }

    public function testProcessorRecipe()
    {
        $container = $this->buildContainer();
        $recipe = $container->get(ProcessorRecipeInterface::class);

        self::assertInstanceOf(
            ProcessorRecipeInterface::class,
            $recipe
        );
    }

    public function testRecipe()
    {
        $container = $this->buildContainer();
        $recipe1 = $container->get(Recipe::class);
        $recipe2 = $container->get(RecipeInterface::class);

        self::assertInstanceOf(
            Recipe::class,
            $recipe1
        );

        self::assertInstanceOf(
            Recipe::class,
            $recipe2
        );

        self::assertSame($recipe1, $recipe2);
    }

    public function testProcessorCookbook()
    {
        $container = $this->buildContainer();
        $cookbook1 = $container->get(ProcessorCookbook::class);
        $cookbook2 = $container->get(ProcessorCookbookInterface::class);

        self::assertInstanceOf(
            ProcessorCookbook::class,
            $cookbook1
        );

        self::assertInstanceOf(
            ProcessorCookbook::class,
            $cookbook2
        );

        self::assertSame($cookbook1, $cookbook2);
    }

    public function testRecipeCookbook()
    {
        $container = $this->buildContainer();
        $container->set(LoggerInterface::class, $this->createMock(LoggerInterface::class));
        $container->set(RouterInterface::class, $this->createMock(RouterInterface::class));
        $cookbook1 = $container->get(Cookbook::class);
        $cookbook2 = $container->get(CookbookInterface::class);

        self::assertInstanceOf(
            Cookbook::class,
            $cookbook1
        );

        self::assertInstanceOf(
            Cookbook::class,
            $cookbook2
        );

        self::assertSame($cookbook1, $cookbook2);
    }
}
