<?php

declare(strict_types=1);

file_put_contents(
    __DIR__. '/issue_8828_a.test-out.php',
    '<?php
if (true) {
    $foo = "'.str_repeat(' text $variable text ', 50_000).'";
}',
);

file_put_contents(
    __DIR__. '/issue_8828_b.test-out.php',
    '<?php
if (true) {
    $foo = "'.str_repeat(' text {$variable} text ', 50_000).'";
}',
);

file_put_contents(
    __DIR__. '/issue_8828_c.test-out.php',
    // smaller sample to avoid memory issues in some environments
    '<?php
if (true) {
    $foo = "'.str_repeat(' text ".$variable." text ', 25_000).'";
}',
);
