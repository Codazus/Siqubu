<?php

namespace Siqubu\Features;

use Siqubu\Expressions\Literal;
use Siqubu\Select;

trait SelectTrait
{
    /**
     * Columns to use.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Set the selected columns.
     *
     * @param string|array $columns Columns to select.<br />
     * - If a string is passed, the value will be quoted,
     * - if a \Siqubu\Literal is passed, he will be rendered,
     * - if a \Siqubu\Select is passed, he will be rendered,
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

        // If $args contains more than 1 data, we add them to existing columns.
        if (1 < func_num_args()) {
            $columns = array_merge($columns, array_slice(func_get_args(), 1));
        }

        $this->columns = array_merge($this->columns, $columns);

        return $this;
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
            }

            if (null !== $field_alias) {
                $field = sprintf('%s.%s', $field_alias, $field_value);
            } else {
                $field = $field_value;
            }

            if (!is_numeric($alias)) {
                $field = sprintf('%s %s', $field, $alias);
            }

            $fields[] = $field;
        }

        return trim(sprintf('SELECT %s', implode(', ', $fields)));
    }
}
