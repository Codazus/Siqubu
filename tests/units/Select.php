<?php

namespace Siqubu\tests\units;

use atoum;
use Siqubu\Select as SelectBuilder;

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
        $expected   .= ' WHERE email LIKE :email AND disabled != 1 AND date_last_connexion > DATE_SUB(NOW(), INTERVAL 3 DAYS)';
        $parameters = [':email' => '%@domain.tld'];

        $builder
            ->whereLike('email', ':email')
            ->whereNot('disabled', true)
            ->whereGt('date_last_connexion','DATE_SUB(NOW(), INTERVAL 3 DAYS)')
            ->setParameters($parameters)
        ;

        $this
            ->string($this->testedInstance->render())
                ->isEqualTo($expected)
            ->array($this->testedInstance->getParameters())
                ->isEqualTo($parameters)
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
            . 'INNER JOIN civilitytitles AS c ON id_civility = c.id LEFT JOIN orders ON users.id = orders.id_user '
            . 'WHERE orders.id != NULL GROUP BY users.id HAVING SUM(total_tax_inclusive) >= 5000 ORDER BY MAX(total_tax_inclusive)';
        $builder    = (new SelectBuilder('users.id', 'firstname', 'lastname', 'c.id', 'title'))
            ->from('users')
            ->innerJoin(['civilitytitles', 'c'], ['id_civility', 'c.id'])
            ->leftJoin('orders', 'users.id = orders.id_user')
            ->whereNot('orders.id', null)
            ->groupBy('users.id')
            ->havingGte('SUM(total_tax_inclusive)', 5000)
            ->orderBy('MAX(total_tax_inclusive)')
        ;

        $this
            ->given($builder)
            ->then
                ->string($builder->render())
                    ->isEqualTo($expected)
        ;
    }
}
