<?php

declare(strict_types=1);

namespace Lemonmind\GridOptionsSaveBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('lemonmind_grid_options_save');

        return $treeBuilder;
    }
}
