<?php

namespace Siqubu\Features;

use InvalidArgumentException;
use Siqubu\Expressions\CloseBracket;
use Siqubu\Expressions\ExpressionsInterface;
use Siqubu\Expressions\OpenBracket;
use Siqubu\Expressions\OrOperator;
use Siqubu\Select;
use SplQueue;

trait JoinTrait
{
    /**
     * JOIN parts.
     *
     * @var SplQueue
     */
    protected $join;

    /**
     * Add a INNER JOIN.
     *
     * @param mixed $table Table of the JOIN
     * @param mixed $conditions Conditions of the JOIN
     *
     * @return JoinTrait
     */
    public function innerJoin($table, $conditions = null)
    {
        return $this->join(self::INNER_JOIN, $table, $conditions, func_get_args());
    }

    /**
     * Add a LEFT JOIN.
     *
     * @param mixed $table Table of the join
     * @param mixed $conditions Conditions of the JOIN
     *
     * @return JoinTrait
     */
    public function leftJoin($table, $conditions = null)
    {
        return $this->join(self::LEFT_JOIN, $table, $conditions, func_get_args());
    }

    /**
     * Add a RIGHT JOIN.
     *
     * @param mixed $table Table of the join
     * @param mixed $conditions Conditions of the JOIN
     *
     * @return JoinTrait
     */
    public function rightJoin($table, $conditions = null)
    {
        return $this->join(self::RIGHT_JOIN, $table, $conditions, func_get_args());
    }

    /**
     * Add a CROSS JOIN.
     *
     * @param mixed $table Table of the join
     * @param mixed $conditions Conditions of the JOIN
     *
     * @return JoinTrait
     */
    public function crossJoin($table, $conditions = null)
    {
        return $this->join(self::CROSS_JOIN, $table, $conditions, func_get_args());
    }

    /**
     * Add a FULL JOIN.
     *
     * @param mixed $table Table of the join
     * @param mixed $conditions Conditions of the JOIN
     *
     * @return JoinTrait
     */
    public function fullJoin($table, $conditions = null)
    {
        return $this->join(self::FULL_JOIN, $table, $conditions, func_get_args());
    }

    /**
     * Add a NATURAL JOIN.
     *
     * @param mixed $table Table of the join
     * @param mixed $conditions Conditions of the JOIN
     *
     * @return JoinTrait
     */
    public function naturalJoin($table, $conditions = null)
    {
        return $this->join(self::NATURAL_JOIN, $table, $conditions, func_get_args());
    }

    /**
     * Add a JOIN (INNER, LEFT, RIGHT, NATURAL, CROSS, FULL).
     *
     * @param string $type Type of JOIN
     * @param mixed $table Table of the join
     * @param mixed $conditions Conditions of the JOIN
     * @param array $args Extra arguments passed
     *
     * @return JoinTrait
     */
    protected function join($type, $table, $conditions = null, array $args = [])
    {
        if (!in_array($type, [
            self::CROSS_JOIN, self::FULL_JOIN, self::INNER_JOIN,
            self::LEFT_JOIN, self::NATURAL_JOIN, self::RIGHT_JOIN
        ])) {
            throw new InvalidArgumentException('Invalid type of JOIN.');
        }

        $queue_conditions = new SplQueue();

        if (null !== $conditions) {
            /*
             * If $args contains more than 2 data (join type and first condition),
             * we add them to existing conditions.
             */
            $conditions = [$conditions];

            if (2 < count($args)) {
                $conditions = array_merge($conditions, array_slice($args, 2));
            }

            // Checks data conditions
            foreach ($conditions as $conditon_data) {
                $queue_conditions->push($conditon_data);
            }
        }

        $this->join->push([
            'type'          => $type,
            'table'         => is_array($table) ? $table : [$table, null],
            'conditions'    => $queue_conditions,
        ]);

        $this->current_expression_queue = $this->join;

        return $this;
    }

    /**
     * Renders the JOIN parts.
     *
     * @return string
     */
    protected function renderJoin()
    {
        if (0 === $this->join->count()) {
            return '';
        }

        $str = '';

        foreach ($this->join as $join_data) {
            list($table, $alias) = $join_data['table'];

            if ($table instanceof Select) {
                $table = sprintf('(%s)', $table->render());
            }

            if (null !== $alias) {
                $table = trim(sprintf('%s AS %s', $table, $alias));
            }

            $str .= trim(sprintf('%s %s', $join_data['type'], $table));

            if (0 === $join_data['conditions']->count()) {
                continue;
            }

            $str .= ' ON ';

            foreach ($join_data['conditions'] as $index => $data) {
                if ($data instanceof ExpressionsInterface) {
                    $str .= $data->render().' ';

                    if (!$data instanceof OrOperator &&
                        !$data instanceof OpenBracket &&
                        $join_data['conditions']->offsetExists($index + 1) &&
                        !$join_data['conditions']->offsetGet($index + 1) instanceof CloseBracket) {
                        $str .= 'AND ';
                    }

                    continue;
                }

                if (is_array($data)) {
                    list($from, $to) = $data;

                    if ($from instanceof Select) {
                        $from = sprintf('(%s)', $from->render());
                    }

                    if ($to instanceof Select) {
                        $to = sprintf('(%s)', $to->render());
                    }

                    $str .= sprintf('%s = %s ', $from, $to);
                } else {
                    $str .= $data.' ';
                }

                /*
                 * If we have another condition part next and it is not a
                 * closing bracket or an OrOperator, we add an AND
                 */
                if ($join_data['conditions']->offsetExists($index + 1) &&
                    !$join_data['conditions']->offsetGet($index + 1) instanceof CloseBracket &&
                    !$join_data['conditions']->offsetGet($index + 1) instanceof OrOperator) {
                    $str .= 'AND ';
                }
            }
        }

        return trim($str);
    }
}
