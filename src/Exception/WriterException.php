<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Exception;

use Exception;
use Throwable;

/**
 * The exception thrown when the serializer writer encountered a problem.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class WriterException extends Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
