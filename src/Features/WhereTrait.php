<?php

namespace Siqubu\Features;

use SplQueue;

trait WhereTrait
{
    /**
     * WHERE parts.
     *
     * @var SplQueue
     */
    protected $where;

    /**
     * Add a WHERE clause.<br />
     * Default will be A = B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     * @param string $operator The clause operator
     *
     * @return WhereTrait
     */
    public function where($left, $right, $operator = '=')
    {
        $this->where->push([
            'left'      => $left,
            'right'     => $right,
            'operator'  => $operator,
        ]);

        $this->current_expression_queue = $this->where;

        return $this;
    }

    /**
     * Add a WHERE clause with A != B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return WhereTrait
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
     * @return WhereTrait
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
     * @return WhereTrait
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
     * @return WhereTrait
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
     * @return WhereTrait
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
     * @return WhereTrait
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
     * @return WhereTrait
     */
    public function whereLte($left, $right)
    {
        return $this->where($left, $right, '<=');
    }

    /**
     * Renders the WHERE parts.
     *
     * @return string
     */
    protected function renderWhere()
    {
        $where = $this->renderWhereOrHaving($this->where);

        if (empty($where)) {
            return '';
        }

        return sprintf('WHERE %s', $where);
    }
}
