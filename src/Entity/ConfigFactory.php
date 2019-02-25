<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity;

use DateTimeImmutable;
use Exception;
use FactorioItemBrowser\Api\Search\Constant\ConfigKey;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The factory of the config entity.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ConfigFactory implements FactoryInterface
{
    /**
     * Creates the config.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return Config
     * @throws ServiceNotCreatedException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return $this->createConfigEntity($config[ConfigKey::PROJECT][ConfigKey::LIBRARY]);
    }

    /**
     * Creates and initializes the config entity.
     * @param array $config
     * @return Config
     * @throws ServiceNotCreatedException
     */
    protected function createConfigEntity(array $config): Config
    {
        try {
            $result = new Config();
            $result->setMaxSearchResults($config[ConfigKey::MAX_SEARCH_RESULTS])
                   ->setMaxCacheAge(new DateTimeImmutable($config[ConfigKey::MAX_CACHE_AGE]))
                   ->setFetcherAliases($config[ConfigKey::FETCHERS])
                   ->setSerializerAliases($config[ConfigKey::SERIALIZERS]);
        } catch (Exception $e) {
            throw new ServiceNotCreatedException('Invalid config for the API search library provided.');
        }
        return $result;
    }
}
