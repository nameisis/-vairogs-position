<?php

namespace Vairogs\Utils\Position\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Vairogs\Utils\DependencyInjection\Component\Definable;

class Definition implements Definable
{
    private const ALLOWED = [
        Definable::POSITION,
        Definable::PAGINATION,
    ];

    public function getExtensionDefinition($extension): ArrayNodeDefinition
    {
        if (!\in_array($extension, self::ALLOWED, true)) {
            throw new InvalidConfigurationException(\sprintf('Invalid extension: %s', $extension));
        }
        switch ($extension) {
            case Definable::POSITION:
                return $this->getPositionDefinition($extension);
            case Definable::PAGINATION:
                return $this->getPaginationDefinition($extension);
        }
    }

    private function getPositionDefinition($extension): ArrayNodeDefinition
    {
        $node = (new TreeBuilder())->root($extension);
        /** @var ArrayNodeDefinition $node */
        // @formatter:off
        $node
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('entities')
                    ->useAttributeAsKey('class')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('manager')->defaultValue('default')->end()
                            ->scalarNode('field')->defaultValue('position')->end()
                        ->end()
                ->end()
            ->end();
        // @formatter:on
        return $node;
    }

    private function getPaginationDefinition($extension): ArrayNodeDefinition
    {
        $node = (new TreeBuilder())->root($extension);
        /** @var ArrayNodeDefinition $node */
        // @formatter:off
        $node
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('behaviour')
                    ->arrayPrototype()
                        ->variablePrototype()->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on
        return $node;
    }
}
