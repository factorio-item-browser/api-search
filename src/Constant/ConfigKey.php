<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Constant;

/**
 * The interface holding the keys of the config.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface ConfigKey
{
    /**
     * The key holding the name of the project.
     */
    public const PROJECT = 'factorio-item-browser';

    /**
     * The key holding the name of the library itself.
     */
    public const LIBRARY = 'api-search';

    /**
     * The key for the fetchers.
     */
    public const FETCHERS = 'fetchers';

    /**
     * The key for the serializers.
     */
    public const SERIALIZERS = 'serializers';
}
