<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SortableBehaviorBundle\DependencyInjection;

use Gedmo\Sortable\SortableListener;
use Runroom\SortableBehaviorBundle\Service\GedmoPositionHandler;
use Runroom\SortableBehaviorBundle\Service\ORMPositionHandler;
use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress PossiblyNullReference, PossiblyUndefinedMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('runroom_sortable_behavior');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->scalarNode('position_handler')
                ->cannotBeEmpty()
                ->defaultValue(class_exists(SortableListener::class) ? GedmoPositionHandler::class : ORMPositionHandler::class)
                ->validate()
                    ->ifTrue(fn (string $config): bool => !is_a($config, PositionHandlerInterface::class, true))
                    ->thenInvalid('%s must implement ' . PositionHandlerInterface::class)
                ->end()
            ->end()
            ->arrayNode('position_field')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('default')
                        ->defaultValue('position')
                    ->end()
                    ->arrayNode('entities')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('sortable_groups')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('entities')
                        ->useAttributeAsKey('name')
                        ->variablePrototype()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
