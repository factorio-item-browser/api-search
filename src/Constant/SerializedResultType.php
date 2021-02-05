<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Constant;

/**
 * The interface holding the types used in the serialized strings.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface SerializedResultType
{
    /**
     * The serialized result is an item.
     */
    public const ITEM = 0x01;

    /**
     * The serialized result is a recipe.
     */
    public const RECIPE = 0x02;
}
