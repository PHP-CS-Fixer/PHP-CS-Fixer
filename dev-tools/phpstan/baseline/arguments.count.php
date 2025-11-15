<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Method PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoUselessElseFixerTest::testFix80() invoked with 2 parameters, 1 required.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/ControlStructure/NoUselessElseFixerTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
