<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Service;

use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use FactorioItemBrowser\Api\Search\Entity\Config;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The factory of the cached search result service.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResultServiceFactory implements FactoryInterface
{
    /**
     * Creates the service.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return CachedSearchResultService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var CachedSearchResultRepository $cachedSearchResultRepository */
        $cachedSearchResultRepository = $container->get(CachedSearchResultRepository::class);
        /* @var Config $config */
        $config = $container->get(Config::class);
        /* @var SerializerService $serializerService */
        $serializerService = $container->get(SerializerService::class);

        return new CachedSearchResultService(
            $cachedSearchResultRepository,
            $serializerService,
            $config->getMaxCacheAge()
        );
    }
}
