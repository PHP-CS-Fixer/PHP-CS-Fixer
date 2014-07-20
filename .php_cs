<?php

return Symfony\CS\Config\Config::create()->finder(Symfony\CS\Finder\DefaultFinder::create()
    ->notName('LICENSE')
    ->notName('README.md')
    ->notName('composer.*')
    ->notName('phpunit.xml*')
    ->notName('*.phar')
    ->exclude('vendor')
    ->exclude('Symfony/CS/Tests/Fixer')
    ->exclude('Symfony/CS/Tests/Fixtures')
    ->notName('phar-stub.php')
    ->notName('ElseifFixer.php')
    ->notName('FunctionDeclarationSpacingFixer.php')
    ->in(__DIR__)
);
