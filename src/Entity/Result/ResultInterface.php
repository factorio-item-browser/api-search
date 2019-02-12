<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity\Result;

/**
 * The interface of the result entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface ResultInterface
{
    /**
     * Returns the type of the result.
     * @return string
     */
    public function getType(): string;

    /**
     * Returns the name of the result.
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the priority of the result.
     * @return int
     */
    public function getPriority(): int;
}
