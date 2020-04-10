<?php

declare(strict_types=1);

// https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.implode-reverse-parameters
// 'implode_call'
$x = implode('', $foo);

// https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.real
// 'no_alias_functions'
$z2 = is_float($v);

// 'use_arrow_functions'
$ids = array_map(fn ($item) => $item->id, $items);
