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

            // Get alias and values data
            list($left_alias, $left_operand)    = $this->getAliasData($data['left']);
            list($right_alias, $right_operand)  = $this->getAliasData($data['right']);

            // Treats the left operand
            if ($left_operand instanceof Literal) {
                $left_operand = $left_operand->render();
            } elseif ($left_operand instanceof Select) {
                $left_operand = sprintf('(%s)', $left_operand->render());
            }

            if (null !== $left_alias) {
                $left = sprintf('%s.%s', $left_alias, $left_operand);
            } else {
                $left = $left_operand;
            }

            // Treats the right operand
            if ($right_operand instanceof Literal) {
                $right_operand = $right_operand->render();
            } elseif ($right_operand instanceof Select) {
                $right_operand = sprintf('(%s)', $right_operand->render());
            } else {
                $right_operand = self::escape($right_operand);
            }

            if (null !== $right_alias) {
                $right = sprintf('%s.%s', $right_alias, $right_operand);
            } else {
                $right = $right_operand;
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
