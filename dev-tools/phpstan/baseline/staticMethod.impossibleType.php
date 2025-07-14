<?php declare(strict_types = 1);

// total 3 errors

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Call to static method PhpCsFixer\\\\Preg\\:\\:match\\(\\) with arguments \'\\#\\^\\.\\*\\?\\(\\?P\\<annotation…\', mixed and array\\{\\} will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Fixer/Internal/ConfigurableFixerTemplateFixer.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to static method PhpCsFixer\\\\Preg\\:\\:match\\(\\) with arguments \'/array\\<\\\\\\\\w\\+,\\\\\\\\s\\*\\(\\\\\\\\\\?\\?\\[…\', string and array\\{\\} will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/FixerConfiguration/FixerConfigurationResolver.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to static method PhpCsFixer\\\\Preg\\:\\:match\\(\\) with arguments \'\\#\\^@PHP\\(\\[\\\\\\\\d\\]\\{2\\}…\', string and array\\{\\} will always evaluate to false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/RuleSet/AbstractMigrationSetDescription.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
