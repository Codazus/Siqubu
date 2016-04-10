<?php

namespace Siqubu\tests\units;

use atoum;
use Siqubu\Select as SelectBuilder;
use Siqubu\Expressions\Literal;

class Select extends atoum\test
{
    /**
     * Test a simple SELECT query.
     */
    public function testSimpleSelect()
    {
        // Select some columns from a users table
        $expected   = 'SELECT id, firstname, lastname FROM users';
        $builder    = $this->newTestedInstance
            ->select('id')
            ->select('firstname')
            ->select('lastname')
            ->from('users')
        ;

        $this
            ->given($this->testedInstance)
            ->then
                ->string($this->testedInstance->render())
                    ->isEqualTo($expected)
        ;

        // Add some WHERE clauses
        $expected .= ' WHERE email LIKE \'%@domain.tld\' AND disabled != 1 AND date_last_connexion > DATE_SUB(NOW(), INTERVAL 3 DAYS)';

        $builder
            ->whereLike('email', '%@domain.tld')
            ->whereNot('disabled', true)
            ->whereGt('date_last_connexion', new Literal('DATE_SUB(NOW(), INTERVAL 3 DAYS)'))
        ;

        $this
            ->string($this->testedInstance->render())
                ->isEqualTo($expected)
        ;

        // Add ORDER BY and LIMIT
        $expected .= ' ORDER BY lastname, firstname LIMIT 10';

        $builder
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->limit(10)
        ;

        $this
            ->string($this->testedInstance->render())
                ->isEqualTo($expected)
        ;
    }

    /**
     * Test an intermediary SELECT query.
     */
    public function testIntermediarySelect()
    {
        $expected   = 'SELECT users.id, firstname, lastname, c.id, title FROM users '
            . 'INNER JOIN civilitytitles c ON id_civility = c.id LEFT JOIN orders ON users.id = orders.id_user '
            . 'WHERE orders.id != NULL GROUP BY users.id HAVING SUM(total_tax_inclusive) >= \'5000\' ORDER BY MAX(total_tax_inclusive)';
        $builder    = (new SelectBuilder(['users' => 'id'], 'firstname', 'lastname', ['c' => 'id'], 'title'))
            ->from('users')
            ->innerJoin(['c' => 'civilitytitles'], ['id_civility', ['c' => 'id']])
            ->leftJoin('orders', [['users' => 'id'], ['orders' => 'id_user']])
            ->whereNot(['orders' => 'id'], null)
            ->groupBy(['users' => 'id'])
            ->havingGte(new Literal('SUM(total_tax_inclusive)'), 5000)
            ->orderBy(new Literal('MAX(total_tax_inclusive)'))
        ;

        $this
            ->given($builder)
            ->then
                ->string($builder->render())
                    ->isEqualTo($expected)
        ;
    }
}
