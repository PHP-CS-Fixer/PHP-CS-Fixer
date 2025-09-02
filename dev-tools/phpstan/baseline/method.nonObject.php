<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Cannot call method getPriority\\(\\) on PhpCsFixer\\\\Fixer\\\\FixerInterface\\|false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/AbstractProxyFixer.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
