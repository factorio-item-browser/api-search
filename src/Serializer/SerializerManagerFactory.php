<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Serializer;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The factory of the serializer manager.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SerializerManagerFactory implements FactoryInterface
{
    /**
     * The serializer classes to use.
     */
    protected const SERIALIZER_CLASSES = [
        ItemResultSerializer::class,
        RecipeResultSerializer::class,
    ];

    /**
     * Creates the serializer manager.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return SerializerManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SerializerManager($this->createSerializers($container));
    }

    /**
     * Creates the serializers to use.
     * @param ContainerInterface $container
     * @return array|SerializerInterface[]
     */
    protected function createSerializers(ContainerInterface $container): array
    {
        $result = [];
        foreach (self::SERIALIZER_CLASSES as $alias) {
            $result[] = $container->get($alias);
        }
        return $result;
    }
}
