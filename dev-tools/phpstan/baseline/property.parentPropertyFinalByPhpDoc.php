<?php declare(strict_types = 1);

// total 2 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultDescription overrides @final property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultDescription\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultName overrides @final property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultName\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
