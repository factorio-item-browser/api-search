<?php

declare(strict_types=1);

/**
 * The configuration of the API search dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Search;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'factories'  => [
            Parser\QueryParser::class => InvokableFactory::class
        ],
    ],
];
