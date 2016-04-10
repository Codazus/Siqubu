<?php
namespace Siqubu\tests\units;

use atoum;
use Siqubu\Select as SelectBuilder;

class Update extends atoum\test
{
    /**
     * Test a simple UPDATE query.
     */
    public function testSimpleUpdate()
    {
        $expected   = 'UPDATE users SET lastname = :lastname, firstname = :firstname';
        $builder    = $this->newTestedInstance('users')
            ->set('lastname', ':lastname')
            ->set('firstname', ':firstname')
            ->setParameters([':firstname' => 'John', ':lastname' => 'Doe'])
        ;

        $this
            ->given($this->testedInstance)
            ->then
                ->string($this->testedInstance->render())
                    ->isEqualTo($expected)
        ;

        // Add some WHERE clauses
        $expected .= ' WHERE email LIKE :email AND disabled != 1 AND date_last_connexion > DATE_SUB(NOW(), INTERVAL 3 DAYS)';

        $builder
            ->whereLike('email', ':email')
            ->whereNot('disabled', true)
            ->whereGt('date_last_connexion', 'DATE_SUB(NOW(), INTERVAL 3 DAYS)')
            ->setParameters([':email' => '%@domain.tld'])
        ;

        $this
            ->string($this->testedInstance->render())
                ->isEqualTo($expected)
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
     * Test an intermediary UPDATE query.
     */
    public function testIntermediaryUpdate()
    {
        $expected = 'UPDATE users '
            . 'INNER JOIN civilitytitles c ON id_civility = c.id LEFT JOIN orders ON users.id = orders.id_user '
            . 'SET firstname = (SELECT firstname FROM members m WHERE m.email = users.email) '
            . 'WHERE orders.id != NULL ORDER BY MAX(total_tax_inclusive)';

        $select_name = (new SelectBuilder('firstname'))
            ->from('members', 'm')
            ->where('m.email', 'users.email')
        ;

        $this->newTestedInstance('users')
            ->innerJoin(['civilitytitles', 'c'], ['id_civility', 'c.id'])
            ->leftJoin('orders', ['users.id', 'orders.id_user'])
            ->set('firstname', $select_name)
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
