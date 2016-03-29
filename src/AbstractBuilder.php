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
     * @var string
     */
    const INNER_JOIN = 'INNER JOIN';

    /**
     * @var string
     */
    const LEFT_JOIN = 'LEFT JOIN';

    /**
     * @var string
     */
    const RIGHT_JOIN = 'RIGHT JOIN';

    /**
     * @var string
     */
    const FULL_JOIN = 'FULL JOIN';

    /**
     * @var string
     */
    const CROSS_JOIN = 'CROSS JOIN';

    /**
     * @var string
     */
    const NATURAL_JOIN = 'NATURAL JOIN';

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
     * JOIN parts.
     *
     * @var array
     */
    protected $joins = [];

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
     * Add a INNER JOIN.
     *
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    public function innerJoin($data, array $conditions = [])
    {
        return $this->join(self::INNER_JOIN, $data, $conditions);
    }

    /**
     * Add a LEFT JOIN.
     *
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    public function leftJoin($data, array $conditions = [])
    {
        return $this->join(self::LEFT_JOIN, $data, $conditions);
    }

    /**
     * Add a RIGHT JOIN.
     *
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    public function rightJoin($data, array $conditions = [])
    {
        return $this->join(self::RIGHT_JOIN, $data, $conditions);
    }

    /**
     * Add a CROSS JOIN.
     *
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    public function crossJoin($data, array $conditions = [])
    {
        return $this->join(self::CROSS_JOIN, $data, $conditions);
    }

    /**
     * Add a FULL JOIN.
     *
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    public function fullJoin($data, array $conditions = [])
    {
        return $this->join(self::FULL_JOIN, $data, $conditions);
    }

    /**
     * Add a NATURAL JOIN.
     *
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    public function naturalJoin($data, array $conditions = [])
    {
        return $this->join(self::NATURAL_JOIN, $data, $conditions);
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
     * Add a JOIN (INNER, LEFT, RIGHT, NATURAL, CROSS, FULL).
     *
     * @param string $type Type of JOIN
     * @param mixed $data Data of the JOIN
     * @param array $conditions Conditions of the JOIN
     *
     * @return AbstractBuilder
     */
    protected function join($type, $data, array $conditions = [])
    {
        if (!in_array($type, [
            self::CROSS_JOIN, self::FULL_JOIN, self::INNER_JOIN,
            self::LEFT_JOIN, self::NATURAL_JOIN, self::RIGHT_JOIN
        ])) {
            throw new InvalidArgumentException('Invalid type of JOIN.');
        }

        // Checks data conditions
        foreach ($conditions as $conditon_data) {
            if (!is_array($conditon_data) || 2 !== count($conditon_data)) {
                throw new InvalidArgumentException('Each JOIN conditions should be an array of two elements.');
            }
        }

        $this->joins[] = [
            'type'          => $type,
            'data'          => $data,
            'conditions'    => $conditions,
        ];

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
        } else {
            $table = self::quote($table);
        }

        return trim(sprintf('FROM %s %s', $table, $alias));
    }

    /**
     * Renders the JOIN parts.
     *
     * @return string
     */
    public function renderJoin()
    {
        $str = [];

        foreach ($this->joins as $join_data) {
            list($alias, $table) = $this->getAliasData($join_data['data']);

            if ($table instanceof Select) {
                $table = sprintf('(%s)', $table->render());
            } else {
                $table = self::quote($table);
            }

            $conditions = [];

            foreach ($join_data['conditions'] as $conditions_data) {
                list($from_alias, $from_table)  = $this->getAliasData($conditions_data[0]);
                list($to_alias, $to_table)      = $this->getAliasData($conditions_data[1]);

                if (null !== $from_alias) {
                    $from = sprintf('%s.%s', self::quote($from_alias), self::quote($from_table));
                } else {
                    $from = self::quote($from_table);
                }

                if ($to_table instanceof Literal) {
                    $to_table = $to_table->render();
                } elseif ($to_table instanceof Select) {
                    $to_table = sprintf('(%s)', $to_table->render());
                } else {
                    $to_table = self::quote($to_table);
                }

                if (null !== $to_alias) {
                    $to = sprintf('%s.%s', self::quote($to_alias), $to_table);
                } else {
                    $to = $to_table;
                }

                $conditions[] = sprintf('%s = %s', $from, $to);
            }

            $str[] = sprintf('%s %s %s ON %s', $join_data['type'], $table, self::quote($alias), trim(implode(' AND ', $conditions)));
        }

        return trim(implode(' ', $str));
    }

    /**
     * Returns the alias information.
     *
     * @param mixed $value Value or associative array with alias and value
     *
     * @return array
     */
    protected function getAliasData($value)
    {
        if (is_array($value)) {
            return [
                key($value),
                current($value),
            ];
        }

        return [
            null,
            $value,
        ];
    }

    /**
     * Quote the value.
     *
     * @param string $value Value to quote
     *
     * @return string
     */
    public static function quote($value)
    {
        if (self::WILDCARD === $value) {
            return $value;
        }

        return self::QUOTE_IDENTIFIER.$value.self::QUOTE_IDENTIFIER;
    }
}
