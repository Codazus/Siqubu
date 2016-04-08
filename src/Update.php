<?php

namespace Siqubu;

/**
 * Update builder.
 */
class Update extends AbstractBuilder
{
    /**
     * Table to update.
     *
     * @var string
     */
    protected $table;

    /**
     * Values to set.
     *
     * @var array
     */
    protected $set_values = [];

    /**
     * Constructor.
     *
     * @param string $table The table to UPDATE
     */
    public function __construct($table)
    {
        parent::__construct();

        $this->table = self::quote($table);
    }

    /**
     * Set a new value to a column.
     *
     * @param string $column Column to set
     * @param mixed $value Value applied to the column
     *
     * @return Update
     */
    public function set($column, $value)
    {
        $this->set_values[$column] = $value;

        return $this;
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    public function render()
    {
        return implode(' ', array_filter([
            $this->renderUpdate(),
            $this->renderJoin(),
            $this->renderSet(),
            $this->renderWhere(),
            $this->renderOrderBy(),
            $this->renderLimit(),
        ]));
    }

    /**
     * Renders the UPDATE part.
     *
     * @return string
     */
    protected function renderUpdate()
    {
        return sprintf('UPDATE %s', $this->table);
    }

    /**
     * Renders the SET parts.
     *
     * @return string
     */
    protected function renderSet()
    {
        $fields = [];

        if (empty($this->set_values)) {
            return '';
        }

        foreach ($this->set_values as $field => $value) {
            list($field_alias, $field_value) = $this->getAliasData($field);

            // If Literal, render as is...
            if ($value instanceof Literal) {
                $value = $value->render();
            // ... if Select, render as is...
            } elseif ($value instanceof Select) {
                $value = sprintf('(%s)', $value->render());
            // ... else escape value.
            } else {
                $value = self::escape($value);
            }

            if (null !== $field_alias) {
                $field = self::quote($field_alias).'.'.self::quote($field_value);
            } else {
                $field = self::quote($field_value);
            }

            $fields[] = sprintf('%s = %s', $field, $value);
        }

        return trim(sprintf('SET %s', implode(', ', $fields)));
    }
}
