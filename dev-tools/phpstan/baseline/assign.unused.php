<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'rawMessage' => 'Value assigned to variable $file is never read.',
    'count' => 1,
    'path' => __DIR__ . '/../../../tests/AutoReview/BinEntryFileTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
