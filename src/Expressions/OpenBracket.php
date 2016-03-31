<?php

namespace YAQB\Expressions;

/**
 * Open bracket
 */
class OpenBracket implements ExpressionsInterface
{
    /**
     * Renders the opening bracket.
     *
     * @return string
     */
    public function render()
    {
        return '(';
    }
}
