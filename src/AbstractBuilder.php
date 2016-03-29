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
     * DEFAULT ESCAPE IDENTIFIER
     */
    const DEFAULT_ESCAPE_IDENTIFIER = "'";

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
    protected $join = [];

    /**
     * WHERE parts.
     *
     * @var array
     */
    protected $where = [];

    /**
     * GROUP BY parts.
     *
     * @var array
     */
    protected $group_by = [];

    /**
     * An instance of DB.
     *
     * @var mixed
     */
    protected static $db;

    /**
     * The quote identifier.
     *
     * @var string
     */
    protected static $quote_identifier = '`';

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
     * Add a WHERE clause.<br />
     * Default will be A = B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     * @param string $operator The clause operator
     *
     * @return AbstractBuilder
     */
    public function where($left, $right, $operator = '=')
    {
        $this->where[] = [
            'left'      => $left,
            'right'     => $right,
            'operator'  => $operator,
        ];

        return $this;
    }

    /**
     * Add a WHERE clause with A != B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereNot($left, $right)
    {
        return $this->where($left, $right, '!=');
    }

    /**
     * Add a WHERE clause with A LIKE B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereLike($left, $right)
    {
        return $this->where($left, $right, 'LIKE');
    }

    /**
     * Add a WHERE clause with A NOT LIKE B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereNotLike($left, $right)
    {
        return $this->where($left, $right, 'NOT LIKE');
    }

    /**
     * Add a WHERE clause with A > B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereGt($left, $right)
    {
        return $this->where($left, $right, '>');
    }

    /**
     * Add a WHERE clause with A >= B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereGte($left, $right)
    {
        return $this->where($left, $right, '>=');
    }

    /**
     * Add a WHERE clause with A < B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereLt($left, $right)
    {
        return $this->where($left, $right, '<');
    }

    /**
     * Add a WHERE clause with A <= B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return AbstractBuilder
     */
    public function whereLte($left, $right)
    {
        return $this->where($left, $right, '<=');
    }

    /**
     * Add a GROUP BY clause.
     *
     * @param mixed $data The GROUP BY data
     *
     * @return AbstractBuilder
     */
    public function groupBy($data)
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        foreach ($data as $key => $value) {
            if (!is_numeric($key)) {
                $value = [$key => $value];
            }

            $this->group_by[] = $value;
        }

        return $this;
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    protected function render()
    {
        return trim(
            $this->renderFrom().' '.
            $this->renderJoin().' '.
            $this->renderWhere().' '.
            $this->renderGroupBy()
        );
    }

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

        $this->join[] = [
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
    protected function renderJoin()
    {
        $str = [];

        foreach ($this->join as $join_data) {
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

            $condition_str = sprintf('%s %s %s', $join_data['type'], $table, self::quote($alias));

            if (!empty($conditions)) {
                $condition_str .= sprintf(' ON %s', trim(implode(' AND ', $conditions)));
            }


            $str[] = $condition_str;
        }

        return trim(implode(' ', $str));
    }

    /**
     * Renders the JOIN parts.
     *
     * @return string
     */
    protected function renderWhere()
    {
        if (empty($this->where)) {
            return '';
        }

        $where = [];

        foreach ($this->where as $data) {
            list($left_alias, $left_operand)    = $this->getAliasData($data['left']);
            list($right_alias, $right_operand)  = $this->getAliasData($data['right']);

            if ($left_operand instanceof Literal) {
                $left_operand = $left_operand->render();
            } elseif ($left_operand instanceof Select) {
                $left_operand = sprintf('(%s)', $left_operand->render());
            } else {
                $left_operand = self::quote($left_operand);
            }

            if (null !== $left_alias) {
                $left = sprintf('%s.%s', self::quote($left_alias), $left_operand);
            } else {
                $left = $left_operand;
            }

            if ($right_operand instanceof Literal) {
                $right_operand = $right_operand->render();
            } elseif ($right_operand instanceof Select) {
                $right_operand = sprintf('(%s)', $right_operand->render());
            } else {
                $right_operand = self::escape($right_operand);
            }

            if (null !== $right_alias) {
                $right = sprintf('%s.%s', self::quote($right_alias), $right_operand);
            } else {
                $right = $right_operand;
            }

            $where[] = sprintf('%s %s %s', $left, $data['operator'], $right);
        }

        return trim(sprintf('WHERE %s ', implode(' AND ', $where)));
    }

    /**
     * Renders the GROUP BY parts.
     *
     * @return string
     */
    protected function renderGroupBy()
    {
        if (empty($this->group_by)) {
            return '';
        }

        $group_by = [];

        foreach ($this->group_by as $group_data) {
            list($alias, $value) = $this->getAliasData($group_data);

            if ($value instanceof Literal) {
                $value = $value->render();
            } elseif ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            } else {
                $value = self::quote($value);
            }

            if (null !== $alias) {
                $value = sprintf('%s.%s', self::quote($alias), $value);
            }

            $group_by[] = $value;
        }

        return trim(sprintf('GROUP BY %s', implode(', ', $group_by)));
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
        if (empty($value)) {
            return null;
        }

        if (static::WILDCARD === $value) {
            return $value;
        }

        return static::$quote_identifier.$value.static::$quote_identifier;
    }

    /**
     * Escapes the value. Uses the DB capability if provided.
     *
     * @param string $value Value to quote
     *
     * @return string
     */
    public static function escape($value)
    {
        if (static::$db instanceof \PDO) {
            return static::$db->quote($value);
        } elseif (static::$db instanceof \mysqli) {
            return sprintf('\'%s\'', static::$db->real_escape_string($value));
        }

        if (null === $value) {
            return 'NULL';
        } elseif (is_bool($value)) {
            return (int) $value;
        } elseif ($value instanceof Literal) {
            return $value->render();
        } elseif ($value instanceof Select) {
            return sprintf('(%s)', $value);
        }

        return static::DEFAULT_ESCAPE_IDENTIFIER.str_replace(static::DEFAULT_ESCAPE_IDENTIFIER, sprintf('\\%s', static::DEFAULT_ESCAPE_IDENTIFIER), $value).static::DEFAULT_ESCAPE_IDENTIFIER;
    }

    /**
     * Set the quote identifier used by the builder.
     *
     * @param string $identifier Identifier to use
     */
    public static function setQuoteIdentifier($identifier)
    {
        static::$quote_identifier = $identifier;
    }

    /**
     * Set the instance of the DB used by the builder to escape values.
     *
     * @param string $db Identifier to use
     */
    public static function setDb($db)
    {
        if (!($db instanceof \PDO || $db instanceof \mysqli)) {
            throw new InvalidArgumentException('The DB must be an instance of \PDO or \mysqli.');
        }

        static::$db = $db;
    }
}
