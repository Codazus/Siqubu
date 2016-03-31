<?php

namespace YAQB;

use YAQB\Expressions\Literal;

/**
 * Select builder.
 */
class Select extends AbstractBuilder
{
    /**
     * Select builder constructor.
     *
     * @param mixed $columns Columns to select
     */
    public function __construct($columns = null)
    {
        parent::__construct();

        if (null !== $columns) {
            $this->select($columns);
        }
    }

    /**
     * Set the selected columns.
     *
     * @param string|array $columns Columns to select.<br />
     * - If a string is passed, the value will be quoted,
     * - if a \YAQB\Literal is passed, he will be rendered,
     * - if a \YAQB\Select is passed, he will be rendered,
     * - if an array is passed the key will be the table name and his value
     * the field.
     *
     * @return Select
     */
    public function select($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    /**
     * Renders the whole query.
     *
     * @return string
     */
    public function render()
    {
        return trim($this->renderSelect().' '.parent::render());
    }

    /**
     * Renders the SELECT part.
     *
     * @return string
     */
    protected function renderSelect()
    {
        $fields = [];

        if (empty($this->columns)) {
            $this->columns = [self::WILDCARD];
        }

        foreach ($this->columns as $alias => $field) {
            list($field_alias, $field_value) = $this->getAliasData($field);

            // If Literal, render as is...
            if ($field_value instanceof Literal) {
                $field_value = $field_value->render();
            // ... if Select, render as is...
            } elseif ($field_value instanceof Select) {
                $field_value = sprintf('(%s)', $field_value->render());
            // ... else quote value.
            } else {
                $field_value = self::quote($field_value);
            }

            if (null !== $field_alias) {
                $field = self::quote($field_alias).'.'.$field_value;
            } else {
                $field = $field_value;
            }

            if (!is_numeric($alias)) {
                $field = $field.' '.self::quote($alias);
            }

            $fields[] = $field;
        }

        return trim(sprintf('SELECT %s', implode(', ', $fields)));
    }
}
