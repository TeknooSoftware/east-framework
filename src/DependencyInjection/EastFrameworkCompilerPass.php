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

namespace Teknoo\East\Framework\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EastFrameworkCompilerPass
 * Compiler pass to inject service container to east framework controller
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class EastFrameworkCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @return EastFrameworkCompilerPass
     */
    public function process(ContainerBuilder $container): EastFrameworkCompilerPass
    {
        $taggedControllerService = $container->findTaggedServiceIds('east.controller.service');

        foreach ($taggedControllerService as $id => $tags) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall(
                'setContainer',
                [new Reference('service_container')]
            );
        }

        return $this;
    }
}