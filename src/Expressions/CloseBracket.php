<?php

namespace YAQB\Expressions;

/**
 * Close bracket
 */
class CloseBracket implements ExpressionsInterface
{
    /**
     * Renders the closing bracket.
     *
     * @return string
     */
    public function render()
    {
        return ') ';
    }
}
