<?php

return Symfony\CS\Config\Config::create()->finder(Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('tests/Symfony/CS/Tests/Fixer')
    ->exclude('tests/Symfony/CS/Tests/Fixtures')
    ->notName('phar-stub.php')
    ->notName('FunctionDeclarationSpacingFixer.php')
    ->in(__DIR__)
);
