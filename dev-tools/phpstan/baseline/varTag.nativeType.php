<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var with type non\\-empty\\-string is not subtype of native type non\\-falsy\\-string\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
