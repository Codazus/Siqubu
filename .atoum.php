<?php

/* @var $runner  mageekguy\atoum\runner */
$runner
    ->setBootstrapFile('vendor/autoload.php')
    ->disableCodeCoverage()
    ->addTestsFromDirectory('tests/units')
;
