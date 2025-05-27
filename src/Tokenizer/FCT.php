<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tokenizer;

abstract class FCT_PHP_ancient
{
    public const T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG = -1;
    public const T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG = -2;
    public const T_ATTRIBUTE = -3;
    public const T_ENUM = -4;
    public const T_MATCH = -5;
    public const T_NULLSAFE_OBJECT_OPERATOR = -6;
    public const T_PUBLIC_SET = -7;
    public const T_PROTECTED_SET = -8;
    public const T_PRIVATE_SET = -9;
    public const T_READONLY = -10;
}

// TODO: PHP 8.0+, when mentioned PHP version is required, remove the class and consts from FCT classes
abstract class FCT_PHP_since_8_00 extends FCT_PHP_ancient
{
    /** @final */
    public const T_ATTRIBUTE = T_ATTRIBUTE;

    /** @final */
    public const T_MATCH = T_MATCH;

    /** @final */
    public const T_NULLSAFE_OBJECT_OPERATOR = T_NULLSAFE_OBJECT_OPERATOR;
}

// TODO: PHP 8.1+, when mentioned PHP version is required, remove the class and consts from FCT classes
abstract class FCT_PHP_since_8_01 extends FCT_PHP_since_8_00
{
    /** @final */
    public const T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG = T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG;

    /** @final */
    public const T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG = T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;

    /** @final */
    public const T_ENUM = T_ENUM;

    /** @final */
    public const T_READONLY = T_READONLY;
}

// TODO: PHP 8.4+, when mentioned PHP version is required, remove the class and consts from FCT classes
abstract class FCT_PHP_since_8_04 extends FCT_PHP_since_8_01
{
    /** @final */
    public const T_PUBLIC_SET = T_PUBLIC_SET;

    /** @final */
    public const T_PROTECTED_SET = T_PROTECTED_SET;

    /** @final */
    public const T_PRIVATE_SET = T_PRIVATE_SET;
}

if (\PHP_VERSION_ID >= 8_04_00) {
    abstract class FCT_interim extends FCT_PHP_since_8_04 {}
} elseif (\PHP_VERSION_ID >= 8_01_00) {
    abstract class FCT_interim extends FCT_PHP_since_8_01 {}
} elseif (\PHP_VERSION_ID >= 8_00_00) {
    abstract class FCT_interim extends FCT_PHP_since_8_00 {}
} else {
    abstract class FCT_interim extends FCT_PHP_before_8_00 {}
}

/**
 * Forward Compatibility Tokens.
 *
 * Class containing tokens that are not present in the lowest supported PHP version,
 * so the code can always use the class constant, instead of checking if the constant is defined
 *
 * @internal
 */
final class FCT extends FCT_interim {}
