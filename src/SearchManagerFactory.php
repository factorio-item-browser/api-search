<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search;

use FactorioItemBrowser\Api\Search\Entity\Config;
use FactorioItemBrowser\Api\Search\Parser\QueryParser;
use FactorioItemBrowser\Api\Search\Service\CachedSearchResultService;
use FactorioItemBrowser\Api\Search\Service\FetcherService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The factory of the search manager.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class SearchManagerFactory implements FactoryInterface
{
    /**
     * Creates the search manager.
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return SearchManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var CachedSearchResultService $cachedSearchResultService */
        $cachedSearchResultService = $container->get(CachedSearchResultService::class);
        /* @var Config $config */
        $config = $container->get(Config::class);
        /* @var FetcherService $fetcherService */
        $fetcherService = $container->get(FetcherService::class);
        /* @var QueryParser $queryParser */
        $queryParser = $container->get(QueryParser::class);

        return new SearchManager(
            $cachedSearchResultService,
            $fetcherService,
            $queryParser,
            $config->getMaxSearchResults()
        );
    }
}
