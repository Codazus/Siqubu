<?php

namespace Siqubu\Features;

use InvalidArgumentException;
use Siqubu\Select;

trait FromTrait
{
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
     * @param string|Select $table Table
     * @param string|null $alias Alias
     *
     * @return FromTrait
     *
     * @throws InvalidArgumentException if the alias is not a valid string
     * @throws InvalidArgumentException if the from is a Select without alias
     */
    public function from($table, $alias = null)
    {
        if (null === $alias && $table instanceof Select) {
            throw new InvalidArgumentException('Every derived table must have its own alias.');
        }

        $this->from = [$alias => $table];

        return $this;
    }

    /**
     * Renders the FROM part.
     *
     * @return string
     */
    protected function renderFrom()
    {
        list($alias, $table) = $this->getAliasData($this->from);

        if ($table instanceof Select) {
            $table = sprintf('(%s)', $table->render());
        }

        return trim(sprintf('FROM %s %s', $table, $alias));
    }
}
