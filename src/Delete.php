<?php

namespace YAQB;

/**
 * Delete builder.
 */
class Delete extends AbstractBuilder
{
    /**
     * Renders the whole query.
     *
     * @return string
     */
    public function render()
    {
        return implode(' ', array_filter([
            'DELETE',
            $this->renderFrom(),
            $this->renderJoin(),
            $this->renderWhere(),
            $this->renderGroupBy(),
            $this->renderOrderBy(),
            $this->renderLimit(),
        ]));
    }
}
