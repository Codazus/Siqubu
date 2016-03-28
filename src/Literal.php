<?php

namespace YAQB;

/**
 * Literal not escape
 */
class Literal
{
    /**
     * Literal
     *
     * @var string
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param string $expression Literal not escape
     */
    public function __construct($expression)
    {
        $this->value = (string) $expression;
    }

    /**
     * Renders the literal.
     *
     * @return string
     */
    public function render()
    {
        return $this->value;
    }
}
