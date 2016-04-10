<?php

use Siqubu\AbstractBuilder;
use Siqubu\Expressions\Literal;
use Siqubu\Select;

class SelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a simple SELECT query.
     */
    public function testSimpleSelect()
    {
        // Select some columns from a users table
        $expected   = 'SELECT `id`, `firstname`, `lastname` FROM `users`';
        $builder    = (new Select())
            ->select('id')
            ->select('firstname')
            ->select('lastname')
            // Another possible writing
            //->select('id', 'firstname', 'lastname')
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
            //->orderBy('lastname', 'firstname')
            ->limit(10)
        ;

        $this->assertEquals($expected, $builder->render());
    }

    /**
     * Test an intermediary SELECT query.
     */
    public function testIntermediarySelect()
    {
        $expected = 'SELECT `users`.`id`, `firstname`, `lastname`, `c`.`id`, `title` FROM `users` '
            . 'INNER JOIN `civilitytitles` `c` ON `id_civility` = `c`.`id` LEFT JOIN `orders` ON `users`.`id` = `orders`.`id_user` '
            . 'WHERE `orders`.`id` != NULL GROUP BY `users`.`id` HAVING SUM(total_tax_inclusive) >= \'5000\' ORDER BY MAX(`total_tax_inclusive`)';

        // Columns can be passed in the constructor
        $builder = (new Select(['users' => 'id'], 'firstname', 'lastname', ['c' => 'id'], 'title'))
            ->from('users')
            ->innerJoin(['c' => 'civilitytitles'], ['id_civility', ['c' => 'id']])
            ->leftJoin('orders', [['users' => 'id'], ['orders' => 'id_user']])
            ->whereNot(['orders' => 'id'], null)
            ->groupBy(['users' => 'id'])
            ->havingGte(new Literal('SUM(total_tax_inclusive)'), 5000)
            ->orderBy(new Literal(sprintf('MAX(%s)', AbstractBuilder::quote('total_tax_inclusive'))))
        ;

        $this->assertEquals($expected, $builder->render());
    }
}