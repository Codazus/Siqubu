<?php

namespace Siqubu\Expressions;

/**
 * OR operator
 */
class OrOperator implements ExpressionsInterface
{
    /**
     * Renders the OR operator.
     *
     * @return string
     */
    public function render()
    {
        return 'OR';
    }
}
