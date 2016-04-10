<?php

namespace Siqubu\Features;

use SplQueue;

trait HavingTrait
{
    /**
     * HAVING parts.
     *
     * @var SplQueue
     */
    protected $having;

    /**
     * Add a HAVING clause.<br />
     * Default will be A = B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     * @param string $operator The clause operator
     *
     * @return HavingTrait
     */
    public function having($left, $right, $operator = '=')
    {
        $this->having->push([
            'left'      => $left,
            'right'     => $right,
            'operator'  => $operator,
        ]);

        $this->current_expression_queue = $this->having;

        return $this;
    }

    /**
     * Add a HAVING clause with A != B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingNot($left, $right)
    {
        return $this->having($left, $right, '!=');
    }

    /**
     * Add a HAVING clause with A LIKE B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingLike($left, $right)
    {
        return $this->having($left, $right, 'LIKE');
    }

    /**
     * Add a HAVING clause with A NOT LIKE B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingNotLike($left, $right)
    {
        return $this->having($left, $right, 'NOT LIKE');
    }

    /**
     * Add a HAVING clause with A > B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingGt($left, $right)
    {
        return $this->having($left, $right, '>');
    }

    /**
     * Add a HAVING clause with A >= B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingGte($left, $right)
    {
        return $this->having($left, $right, '>=');
    }

    /**
     * Add a HAVING clause with A < B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingLt($left, $right)
    {
        return $this->having($left, $right, '<');
    }

    /**
     * Add a HAVING clause with A <= B.
     *
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     *
     * @return HavingTrait
     */
    public function havingLte($left, $right)
    {
        return $this->having($left, $right, '<=');
    }

    /**
     * Renders the HAVING parts.
     *
     * @return string
     */
    protected function renderHaving()
    {
        $having = $this->renderWhereOrHaving($this->having);

        if (empty($having)) {
            return '';
        }

        return sprintf('HAVING %s', $having);
    }
}
