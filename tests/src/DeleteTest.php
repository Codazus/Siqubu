<?php

use Siqubu\AbstractBuilder;
use Siqubu\Expressions\Literal;
use Siqubu\Delete;

class DeleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a simple DELETE query.
     */
    public function testSimpleDelete()
    {
        $expected   = 'DELETE FROM `users`';
        $builder    = (new Delete())
            ->from('users')
        ;

        $this->assertEquals($expected, $builder->render());

        // Add some WHERE clauses
        $expected .= ' WHERE `email` LIKE \'%@domain.tld\' AND `disabled` != 1 AND `date_last_connexion` > DATE_SUB(NOW(), INTERVAL 3 DAYS)';

        $builder
            ->whereLike('email', '%@domain.tld')
            ->whereNot('disabled', true)
            ->whereGt('date_last_connexion', new Literal('DATE_SUB(NOW(), INTERVAL 3 DAYS)'))
        ;

        $this->assertEquals($expected, $builder->render());

        // Add ORDER BY and LIMIT
        $expected .= ' ORDER BY `lastname`, `firstname` LIMIT 10';

        $builder
            ->orderBy('lastname')
            ->orderBy('firstname')
            // Another possible writing
            //->orderBy(['lastname', 'firstname'])
            ->limit(10)
        ;

        $this->assertEquals($expected, $builder->render());
    }

    /**
     * Test an intermediary DELETE query.
     */
    public function testIntermediaryDelete()
    {
        $expected = 'DELETE FROM `users` '
            . 'INNER JOIN `civilitytitles` `c` ON `id_civility` = `c`.`id` LEFT JOIN `orders` ON `users`.`id` = `orders`.`id_user` '
            . 'WHERE `orders`.`id` != NULL ORDER BY MAX(`total_tax_inclusive`)';

        $builder = (new Delete())
            ->from('users')
            ->innerJoin(['c' => 'civilitytitles'], [['id_civility', ['c' => 'id']]])
            ->leftJoin('orders', [[['users' => 'id'], ['orders' => 'id_user']]])
            ->whereNot(['orders' => 'id'], null)
            ->orderBy(new Literal(sprintf('MAX(%s)', AbstractBuilder::quote('total_tax_inclusive'))))
        ;

        $this->assertEquals($expected, $builder->render());
    }
}
