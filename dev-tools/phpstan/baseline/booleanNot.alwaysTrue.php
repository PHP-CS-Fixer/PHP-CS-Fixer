<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Negated boolean expression is always true.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Tokenizer/Tokens.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
