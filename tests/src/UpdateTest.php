<?php

use Siqubu\AbstractBuilder;
use Siqubu\Expressions\Literal;
use Siqubu\Select;
use Siqubu\Update;

class UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a simple UPDATE query.
     */
    public function testSimpleUpdate()
    {
        $expected   = 'UPDATE `users` SET `lastname` = \'Doe\', `firstname` = \'John\'';
        $builder    = (new Update('users'))
            ->set('lastname', 'Doe')
            ->set('firstname', 'John')
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
            ->orderBy(['lastname', 'firstname'])
            ->limit(10)
        ;

        $this->assertEquals($expected, $builder->render());
    }

    /**
     * Test an intermediary UPDATE query.
     */
    public function testIntermediaryUpdate()
    {
        $expected = 'UPDATE `users` '
            . 'INNER JOIN `civilitytitles` `c` ON `id_civility` = `c`.`id` LEFT JOIN `orders` ON `users`.`id` = `orders`.`id_user` '
            . 'SET `firstname` = (SELECT `firstname` FROM `members` WHERE `email` = `users`.`email`) '
            . 'WHERE `orders`.`id` != NULL ORDER BY MAX(`total_tax_inclusive`)';

        $select_name    = (new Select('firstname'))
            ->from('members')
            ->where('email', ['users' => new Literal(AbstractBuilder::quote('email'))])
        ;
        $builder        = (new Update('users'))
            ->innerJoin(['c' => 'civilitytitles'], ['id_civility', ['c' => 'id']])
            ->leftJoin('orders', [['users' => 'id'], ['orders' => 'id_user']])
            ->set('firstname', $select_name)
            ->whereNot(['orders' => 'id'], null)
            ->orderBy(new Literal(sprintf('MAX(%s)', AbstractBuilder::quote('total_tax_inclusive'))))
        ;

        $this->assertEquals($expected, $builder->render());
    }
}
