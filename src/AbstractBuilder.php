<?php

namespace Siqubu;

use InvalidArgumentException;
use mysqli;
use PDO;
use Siqubu\Expressions\CloseBracket;
use Siqubu\Expressions\Literal;
use Siqubu\Expressions\OpenBracket;
use Siqubu\Expressions\OrOperator;
use SplQueue;

/**
 * Abstract builder used by Select, Update, Insert and Delete.
 */
abstract class AbstractBuilder
{
    /**
     * DEFAULT ESCAPE IDENTIFIER
     */
    const DEFAULT_ESCAPE_IDENTIFIER = "'";

    /**
     * The current SplQueue where to push ExpressionsInterface.
     *
     * @var SplQueue
     */
    protected $current_expression_queue;

    /**
     * An instance of DB.
     *
     * @var mixed
     */
    protected static $db;

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
        if (static::$db instanceof PDO) {
            return static::$db->quote($value);
        } elseif (static::$db instanceof mysqli) {
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
        if (!($db instanceof PDO || $db instanceof mysqli)) {
            throw new InvalidArgumentException('The DB must be an instance of \PDO or \mysqli.');
        }

        static::$db = $db;
    }
}
