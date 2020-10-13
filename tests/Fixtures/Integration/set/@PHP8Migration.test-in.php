<?php
declare(strict_types=1);

$foo = 3;
$bar = (unset) $foo;
$foo = $bar{1};
