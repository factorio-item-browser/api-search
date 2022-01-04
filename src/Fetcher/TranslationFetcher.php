<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Fetcher;

use BluePsyduck\MapperManager\MapperManagerInterface;
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
    public function __construct(
        private readonly MapperManagerInterface $mapperManager,
        private readonly TranslationRepository $translationRepository,
    ) {
    }

    public function fetch(Query $query, AggregatingResultCollection $searchResults): void
    {
        $translations = $this->translationRepository->findDataByKeywords(
            $query->getCombinationId(),
            $query->getLocale(),
            $query->getTerms()->getValuesByTypes([TermType::GENERIC]),
        );

        foreach ($translations as $translation) {
            if ($translation->getType() === EntityType::RECIPE) {
                $searchResults->addRecipe($this->mapperManager->map($translation, new RecipeResult()));
            } else {
                $searchResults->addItem($this->mapperManager->map($translation, new ItemResult()));
            }
        }
    }
}
