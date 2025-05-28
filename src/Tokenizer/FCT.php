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

/*
 * Forward Compatibility Tokens
 *
 * Class containing tokens that are not present in the lowest supported PHP version,
 * so the code can always use the class constant, instead of checking if the constant is defined
 */
if (\PHP_VERSION_ID >= 8_04_00) {
    /**
     * @internal
     */
    final class FCT
    {
        public const T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG = T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG;
        public const T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG = T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;
        public const T_ATTRIBUTE = T_ATTRIBUTE;
        public const T_ENUM = T_ENUM;
        public const T_MATCH = T_MATCH;
        public const T_NULLSAFE_OBJECT_OPERATOR = T_NULLSAFE_OBJECT_OPERATOR;
        public const T_PUBLIC_SET = T_PUBLIC_SET;
        public const T_PROTECTED_SET = T_PROTECTED_SET;
        public const T_PRIVATE_SET = T_PRIVATE_SET;
        public const T_READONLY = T_READONLY;
    }
} elseif (\PHP_VERSION_ID >= 8_01_00) {
    /**
     * @internal
     */
    final class FCT
    {
        public const T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG = T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG;
        public const T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG = T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG;
        public const T_ATTRIBUTE = T_ATTRIBUTE;
        public const T_ENUM = T_ENUM;
        public const T_MATCH = T_MATCH;
        public const T_NULLSAFE_OBJECT_OPERATOR = T_NULLSAFE_OBJECT_OPERATOR;
        public const T_PUBLIC_SET = -1;
        public const T_PROTECTED_SET = -2;
        public const T_PRIVATE_SET = -3;
        public const T_READONLY = T_READONLY;
    }
} elseif (\PHP_VERSION_ID >= 8_00_00) {
    /**
     * @internal
     */
    final class FCT
    {
        public const T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG = -1;
        public const T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG = -2;
        public const T_ATTRIBUTE = T_ATTRIBUTE;
        public const T_ENUM = -3;
        public const T_MATCH = T_MATCH;
        public const T_NULLSAFE_OBJECT_OPERATOR = T_NULLSAFE_OBJECT_OPERATOR;
        public const T_PUBLIC_SET = -4;
        public const T_PROTECTED_SET = -5;
        public const T_PRIVATE_SET = -6;
        public const T_READONLY = -7;
    }
} else {
    /**
     * @internal
     */
    final class FCT
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
}
