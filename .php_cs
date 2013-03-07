<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;

$finder = DefaultFinder::create()
    ->notName('LICENSE')
    ->notName('README.md')
    ->notName('.php_cs')
    ->notName('composer.*')
    ->notName('phpunit.xml*')
    ->notName('*.phar')
    ->exclude('vendor')
    ->exclude('Symfony/CS/Tests/Fixer')
    ->exclude('Symfony/CS/Tests/Fixtures')
    ->notName('ElseifFixer.php')
    ->in(__DIR__);

return Config::create()->finder($finder);
