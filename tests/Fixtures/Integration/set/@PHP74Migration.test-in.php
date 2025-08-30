<?php

declare(strict_types=1);

// https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.array-string-access-curly-brace
// 'normalize_index_brace'
$y = $f{1};

// https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.real
$a = (real) $a;
