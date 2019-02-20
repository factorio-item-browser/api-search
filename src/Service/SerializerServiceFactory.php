<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use FactorioItemBrowser\Api\Search\Serializer\ItemResultSerializer;
use FactorioItemBrowser\Api\Search\Serializer\RecipeResultSerializer;
use FactorioItemBrowser\Api\Search\Serializer\SerializerInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The factory of the serializer service.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SerializerServiceFactory implements FactoryInterface
{
    /**
     * The serializer classes to use.
     */
    protected const SERIALIZER_CLASSES = [
        ItemResultSerializer::class,
        RecipeResultSerializer::class,
    ];

    /**
     * Creates the service.
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return SerializerService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SerializerService($this->createSerializers($container));
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
