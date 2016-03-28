<?php

namespace YAQB;

use InvalidArgumentException;

/**
 * Abstract builder used by Select, Update, Insert and Delete.
 */
abstract class AbstractBuilder
{
    /**
     * WILDCARD
     */
    const WILDCARD = '*';

    /**
     * QUOTE IDENTIFIER
     */
    const QUOTE_IDENTIFIER = '`';

    /**
     * Columns to use.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * FROM part.
     *
     * @var array
     */
    protected $from;

    /**
     * Set the from part. Can be an instance of \YABQ\Builder\Select or a
     * string. If the $data is an associative array, the alias must be the key.
     *
     * @param Select $from FROM data
     *
     * @return AbstractBuilder
     *
     * @throws InvalidArgumentException if the alias is not a valid string
     * @throws InvalidArgumentException if the from is a Select without alias
     */
    public function from($from)
    {
        $alias = null;

        if (is_array($from)) {
            $alias = key($from);

            if (is_numeric($alias)) {
                throw new InvalidArgumentException('Alias must be a valid string.');
            }

            $from = current($from);
        }

        if (empty($alias) && $from instanceof Select) {
            throw new InvalidArgumentException('Every derived table must have its own alias.');
        }

        $this->from = [$alias => $from];

        return $this;
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    abstract public function render();

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Renders the FROM part.
     *
     * @return string
     */
    protected function renderFrom()
    {
        $from = current($this->from);

        if ($from instanceof Select) {
            $from = sprintf('(%s)', $from->render());
        } else {
            $from = $this->quote($from);
        }

        return trim(sprintf('FROM %s %s', $from, key($this->from)));
    }

    /**
     * Quote the value.
     *
     * @param string $value Value to quote
     *
     * @return string
     */
    protected function quote($value)
    {
        if (self::WILDCARD === $value) {
            return $value;
        }

        return self::QUOTE_IDENTIFIER.$value.self::QUOTE_IDENTIFIER;
    }
}
