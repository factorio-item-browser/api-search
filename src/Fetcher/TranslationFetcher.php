<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\Exception\MapperException;
use BluePsyduck\MapperManager\MapperManagerInterface;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Repository\TranslationRepository;
use FactorioItemBrowser\Api\Search\Collection\AggregatingResultCollection;
use FactorioItemBrowser\Api\Search\Constant\TermType;
use FactorioItemBrowser\Api\Search\Entity\Query;
use FactorioItemBrowser\Api\Search\Entity\Result\ItemResult;
use FactorioItemBrowser\Api\Search\Entity\Result\RecipeResult;
use FactorioItemBrowser\Common\Constant\EntityType;

/**
 * The class fetching results based on their translations.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslationFetcher implements FetcherInterface
{
    /**
     * The mapper manager.
     * @var MapperManagerInterface
     */
    protected $mapperManager;

    /**
     * The translation repository.
     * @var TranslationRepository
     */
    protected $translationRepository;

    /**
     * Initializes the data fetcher.
     * @param MapperManagerInterface $mapperManager
     * @param TranslationRepository $translationRepository
     */
    public function __construct(MapperManagerInterface $mapperManager, TranslationRepository $translationRepository)
    {
        $this->mapperManager = $mapperManager;
        $this->translationRepository = $translationRepository;
    }

    /**
     * Fetches the data matching the specified query.
     * @param Query $query
     * @param AggregatingResultCollection $searchResults
     * @throws MapperException
     */
    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $translations = $this->fetchTranslations($query);
        foreach ($translations as $translation) {
            if ($translation->getType() === EntityType::RECIPE) {
                $searchResults->addRecipe($this->mapTranslationToRecipe($translation));
            } else {
                $searchResults->addItem($this->mapTranslationToItem($translation));
            }
        }
    }

    /**
     * Fetches the translations matching the query.
     * @param Query $query
     * @return array|TranslationPriorityData[]
     */
    protected function fetchTranslations(Query $query): array
    {
        return $this->translationRepository->findDataByKeywords(
            $query->getCombinationId(),
            $query->getLocale(),
            $query->getTermValuesByType(TermType::GENERIC)
        );
    }

    /**
     * Maps the translation to a item result.
     * @param TranslationPriorityData $translation
     * @return ItemResult
     * @throws MapperException
     */
    protected function mapTranslationToItem(TranslationPriorityData $translation): ItemResult
    {
        $result = new ItemResult();
        $this->mapperManager->map($translation, $result);
        return $result;
    }

    /**
     * Maps the translation to a recipe result.
     * @param TranslationPriorityData $translation
     * @return RecipeResult
     * @throws MapperException
     */
    protected function mapTranslationToRecipe(TranslationPriorityData $translation): RecipeResult
    {
        $result = new RecipeResult();
        $this->mapperManager->map($translation, $result);
        return $result;
    }
}
