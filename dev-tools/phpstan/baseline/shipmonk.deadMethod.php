<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\Tests\\Benchmark\\FixersBench::benchSingleRule',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Benchmark/FixersBench.php',
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
$ignoreErrors[] = [
    'rawMessage' => 'Unused PhpCsFixer\\Tests\\Benchmark\\TokenizerBench::benchTokenization',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/Benchmark/TokenizerBench.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
