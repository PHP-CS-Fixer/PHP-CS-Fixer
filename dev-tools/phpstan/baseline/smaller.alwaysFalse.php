<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Comparison operation "\\<" between int\\<70400, 79999\\>\\|int\\<80001, 80499\\> and 70400 is always false\\.$#',
    'count' => 1,
    'path' => __DIR__ . '/../../../php-cs-fixer',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
