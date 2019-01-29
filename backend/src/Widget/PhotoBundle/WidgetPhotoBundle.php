<?php
namespace Widget\PhotoBundle;

use Backend\BaseBundle\CM4\CM4WidgetInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WidgetPhotoBundle extends Bundle implements CM4WidgetInterface
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DependencyInjection\Compiler\ResourceCompilerPass());
    }
}
