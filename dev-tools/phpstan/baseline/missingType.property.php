<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\CheckCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/CheckCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\DescribeCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DescribeCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\DocumentationCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/DocumentationCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\FixCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/FixCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\HelpCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/HelpCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\ListFilesCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/ListFilesCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\ListSetsCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/ListSetsCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\SelfUpdateCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/SelfUpdateCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\WorkerCommand\\:\\:\\$defaultDescription has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/WorkerCommand.php',
];
$ignoreErrors[] = [
    'message' => '#^Property PhpCsFixer\\\\Console\\\\Command\\\\WorkerCommand\\:\\:\\$defaultName has no type specified\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../src/Console/Command/WorkerCommand.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
