<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Cannot instantiate interface Ergebnis\\AgentDetector\\Detector.',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/ConfigurationResolver.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
