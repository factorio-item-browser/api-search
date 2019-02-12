<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Search\Entity;

/**
 * The class representing a term of the search query.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Term
{
    /**
     * The type of the term.
     * @var string
     */
    protected $type;

    /**
     * The value of the term.
     * @var string
     */
    protected $value;

    /**
     * Initializes the term.
     * @param string $type
     * @param string $value
     */
    public function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Sets the type of the term.
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the term.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the value of the term.
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Returns the value of the term.
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
