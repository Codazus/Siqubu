<?php

namespace Siqubu\Expressions;

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
