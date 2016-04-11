<?php

namespace Siqubu\tests\units;

use atoum;

class Delete extends atoum\test
{
    /**
     * Test a simple DELETE query.
     */
    public function testSimpleDelete()
    {
        $expected   = 'DELETE FROM users';
        $builder    = $this->newTestedInstance
            ->from('users')
        ;

        $this
            ->given($this->testedInstance)
            ->then
                ->string($this->testedInstance->render())
                    ->isEqualTo($expected)
        ;

        // Add some WHERE clauses
        $expected   .= ' WHERE email LIKE :email AND disabled != :disabled AND date_last_connexion > DATE_SUB(NOW(), INTERVAL 3 DAYS)';
        $parameters = [':email' => '%@domain.tld', ':disabled' => true];

        $builder
            ->whereLike('email', ':email')
            ->whereNot('disabled', ':disabled')
            ->whereGt('date_last_connexion', 'DATE_SUB(NOW(), INTERVAL 3 DAYS)')
            ->setParameters($parameters)
        ;

        $this
            ->string($this->testedInstance->render())
                ->isEqualTo($expected)
            ->integer(count($this->testedInstance->getParameters()))
                ->isEqualTo(2)
        ;

        // Add ORDER BY and LIMIT
        $expected .= ' ORDER BY lastname, firstname LIMIT 10';

        $builder
            ->orderBy('lastname', 'firstname')
            ->limit(10)
        ;

        $this
            ->string($this->testedInstance->render())
                ->isEqualTo($expected)
        ;
    }

    /**
     * Test an intermediary DELETE query.
     */
    public function testIntermediaryDelete()
    {
        $expected = 'DELETE FROM users '
            . 'INNER JOIN civilitytitles AS c ON id_civility = c.id LEFT JOIN orders ON users.id = orders.id_user '
            . 'WHERE orders.id != NULL ORDER BY MAX(total_tax_inclusive)';

        $this->newTestedInstance
            ->from('users')
            ->innerJoin(['civilitytitles', 'c'], ['id_civility', 'c.id'])
            ->leftJoin('orders', ['users.id', 'orders.id_user'])
            ->whereNot('orders.id', null)
            ->orderBy('MAX(total_tax_inclusive)')
        ;

        $this
            ->given($this->testedInstance)
            ->then
                ->string($this->testedInstance->render())
                    ->isEqualTo($expected)
        ;
    }
}
