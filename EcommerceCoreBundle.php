<?php

namespace Ecommerce\Bundle\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Ecommerce\Bundle\CoreBundle\DependencyInjection\Compiler\ProductHandlersPass;

class EcommerceCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProductHandlersPass());
    }
}
