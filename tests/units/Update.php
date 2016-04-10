<?php
namespace Siqubu\tests\units;

use atoum;
use Siqubu\Expressions\Literal;
use Siqubu\Select as SelectBuilder;

class Update extends atoum\test
{
    /**
     * Test a simple UPDATE query.
     */
    public function testSimpleUpdate()
    {
        $expected   = 'UPDATE users SET lastname = \'Doe\', firstname = \'John\'';
        $builder    = $this->newTestedInstance('users')
            ->set('lastname', 'Doe')
            ->set('firstname', 'John')
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
            ->orderBy(['lastname', 'firstname'])
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
            . 'SET firstname = (SELECT firstname FROM members WHERE email = users.email) '
            . 'WHERE orders.id != NULL ORDER BY MAX(total_tax_inclusive)';

        $select_name = (new SelectBuilder('firstname'))
            ->from('members')
            ->where('email', ['users' => new Literal('email')])
        ;

        $this->newTestedInstance('users')
            ->innerJoin(['c' => 'civilitytitles'], ['id_civility', ['c' => 'id']])
            ->leftJoin('orders', [['users' => 'id'], ['orders' => 'id_user']])
            ->set('firstname', $select_name)
            ->whereNot(['orders' => 'id'], null)
            ->orderBy(new Literal('MAX(total_tax_inclusive)'))
        ;

        $this
            ->given($this->testedInstance)
            ->then
                ->string($this->testedInstance->render())
                    ->isEqualTo($expected)
        ;
    }
}
