<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Value assigned to variable $token is never read before being overwritten.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Doctrine/Annotation/Tokens.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Value assigned to variable $result is never read before being overwritten.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Preg.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
