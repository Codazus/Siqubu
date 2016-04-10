<?php

namespace Siqubu;

use InvalidArgumentException;
use Siqubu\Expressions\CloseBracket;
use Siqubu\Expressions\OpenBracket;
use Siqubu\Expressions\OrOperator;
use SplQueue;

/**
 * Abstract builder used by Select, Update, Insert and Delete.
 */
abstract class AbstractBuilder
{
    /**
     * The current SplQueue where to push ExpressionsInterface.
     *
     * @var SplQueue
     */
    protected $current_expression_queue;

    /**
     * Query parameters
     *
     * @var array
     *
     * @return AbstractBuilder
     */
    protected $parameters = [];

    /**
     * Defines query parameters.
     *
     * @param array $parameters Query parameters
     *
     * @return AbstractBuilder
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (is_numeric($key)) {
                throw new InvalidArgumentException('Each parameters must have a nominative key.');
            }
        }

        $this->parameters += $parameters;

        return $this;
    }

    /**
     * Returns query parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Open a bracket.
     *
     * @return AbstractBuilder
     */
    public function openBracket()
    {
        $this->current_expression_queue->push(new OpenBracket());

        return $this;
    }

    /**
     * Close a bracket.
     *
     * @return AbstractBuilder
     */
    public function closeBracket()
    {
        $this->current_expression_queue->push(new CloseBracket());

        return $this;
    }

    /**
     * Add an OR operator.
     *
     * @return AbstractBuilder
     */
    public function orOperator()
    {
        $this->current_expression_queue->push(new OrOperator());

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    abstract protected function render();
}
