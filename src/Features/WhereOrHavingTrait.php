<?php

namespace Siqubu\Features;

use Siqubu\Expressions\CloseBracket;
use Siqubu\Expressions\ExpressionsInterface;
use Siqubu\Expressions\Literal;
use Siqubu\Expressions\OpenBracket;
use Siqubu\Expressions\OrOperator;
use Siqubu\Select;
use SplQueue;

trait WhereOrHavingTrait
{
    /**
     * Renders the WHERE or HAVING parts.
     *
     * @return string
     */
    protected function renderWhereOrHaving(SplQueue $render_data)
    {
        if (0 === $render_data->count()) {
            return '';
        }

        $str = '';

        foreach ($render_data as $index => $data) {
            if ($data instanceof ExpressionsInterface) {
                $str .= $data->render().' ';

                if (!$data instanceof OrOperator &&
                    !$data instanceof OpenBracket &&
                    $render_data->offsetExists($index + 1) &&
                    !$render_data->offsetGet($index + 1) instanceof CloseBracket) {
                    $str .= 'AND ';
                }

                continue;
            }

            // Treats the left operand
            if ($data['left'] instanceof Literal) {
                $left = $data['left']->render();
            } elseif ($data['left'] instanceof Select) {
                $left = sprintf('(%s)', $data['left']->render());
            } else {
                $left = $data['left'];
            }

            // Treats the right operand
            if ($data['right'] instanceof Literal) {
                $right = $data['right']->render();
            } elseif ($data['right'] instanceof Select) {
                $right = sprintf('(%s)', $data['right']->render());
            } else {
                $right = null === $data['right'] ? 'NULL' : $data['right'];
            }

            $str .= sprintf('%s %s %s ', $left, $data['operator'], $right);

            /*
             * If we have another WHERE part next and it is not a closing
             * bracket or an OrOperator, we add an AND
             */
            if ($render_data->offsetExists($index + 1) &&
                !$render_data->offsetGet($index + 1) instanceof CloseBracket &&
                !$render_data->offsetGet($index + 1) instanceof OrOperator) {
                $str .= 'AND ';
            }
        }

        return trim($str);
    }
}
