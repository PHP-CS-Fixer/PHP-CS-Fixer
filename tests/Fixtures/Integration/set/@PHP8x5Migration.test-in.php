<?php

// https://wiki.php.net/rfc/deprecations_php_8_5#deprecate_semicolon_after_case_in_switch_statement
switch ($value) {
    case 'foo';
    case 'bar':
    case 'baz';
        echo 'foo, bar, or baz';
        break;
    default;
        echo 'Other';
}
