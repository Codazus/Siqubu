<?php

namespace YAQB;

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
        $query = $this->renderSelect().' '.
            $this->renderFrom().' '
        ;

        return trim($query);
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
            // If Literal, render as is...
            if ($field instanceof Literal) {
                $field = $field->render();
            // ... if associative array, key = table and value = field
            } elseif (is_array($field)) {
                $field = $this->quote(key($field)).'.'.$this->quote(current($field));
            // ... else only field
            } else {
                $field = $this->quote($field);
            }

            if (!is_numeric($alias)) {
                $fields[] = $field.' '.$this->quote($alias);
            } else {
                $fields[] = $field;
            }
        }

        return trim(sprintf('SELECT %s', implode(', ', $fields)));
    }
}
