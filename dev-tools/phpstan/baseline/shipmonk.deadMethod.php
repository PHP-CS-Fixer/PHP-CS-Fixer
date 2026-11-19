<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\UnsupportedPhpVersionAllowedConfigInterface::setUnsupportedPhpVersionAllowed',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/UnsupportedPhpVersionAllowedConfigInterface.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\Tests\\Benchmark\\FixersBench::provideFixerNames',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Benchmark/FixersBench.php',
];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\Tests\\Benchmark\\FixersBench::setUp',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Benchmark/FixersBench.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
