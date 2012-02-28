<?php

namespace Pogotc\RestInABoxBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PogotcRestInABoxExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = array();
	    foreach ($configs as $subConfig) {
	        $config = array_merge($config, $subConfig);
	    }
		$container->setParameter("pogotc_rest_in_a_box.rest_objects", $config);
    }
}
