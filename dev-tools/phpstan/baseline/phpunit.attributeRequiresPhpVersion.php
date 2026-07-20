<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Version requirement is incomplete.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Fixer/Phpdoc/PhpdocTagNoNamedArgumentsFixerTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
