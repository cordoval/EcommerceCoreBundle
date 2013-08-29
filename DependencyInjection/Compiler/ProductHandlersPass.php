<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds all product handlers to the manager
 */
class ProductHandlersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ecommerce.product.handler_manager')) {
            return;
        }

        $productHandlers = new \SplPriorityQueue();
        foreach ($container->findTaggedServiceIds('ecommmerce.product_handler') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $productHandlers->insert(new Reference($id), $priority);
        }

        $productHandlers = iterator_to_array($productHandlers);
        ksort($productHandlers);

        $container->getDefinition('ecommerce.product.handler_manager')->replaceArgument(0, array_values($productHandlers));
    }
}
