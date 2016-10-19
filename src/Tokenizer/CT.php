<?php

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

use PhpCsFixer\Utils;
use Symfony\Component\Finder\Finder;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CT
{
    const 10001 = ARRAY_TYPEHINT;
    const 10002 = BRACE_CLASS_INSTANTIATION_OPEN;
    const 10003 = BRACE_CLASS_INSTANTIATION_CLOSE;
    const 10004 = CLASS_CONSTANT;
    const 10005 = CURLY_CLOSE;
    const 10006 = DOLLAR_CLOSE_CURLY_BRACES;
    const 10007 = DYNAMIC_PROP_BRACE_OPEN;
    const 10008 = DYNAMIC_PROP_BRACE_CLOSE;
    const 10009 = DYNAMIC_VAR_BRACE_OPEN;
    const 10010 = DYNAMIC_VAR_BRACE_CLOSE;
    const 10011 = ARRAY_INDEX_CURLY_BRACE_OPEN;
    const 10012 = ARRAY_INDEX_CURLY_BRACE_CLOSE;
    const 10013 = GROUP_IMPORT_BRACE_OPEN;
    const 10014 = GROUP_IMPORT_BRACE_CLOSE;
    const 10015 = CONST_IMPORT;
    const 10016 = FUNCTION_IMPORT;
    const 10017 = NAMESPACE_OPERATOR;
    const 10018 = NULLABLE_TYPE;
    const 10019 = RETURN_REF;
    const 10020 = ARRAY_SQUARE_BRACE_OPEN;
    const 10021 = ARRAY_SQUARE_BRACE_CLOSE;
    const 10022 = DESTRUCTURING_SQUARE_BRACE_OPEN;
    const 10023 = DESTRUCTURING_SQUARE_BRACE_CLOSE;
    const 10024 = TYPE_ALTERNATION;
    const 10025 = TYPE_COLON;
    const 10026 = USE_TRAIT;
    const 10027 = USE_LAMBDA;

    private function __constructor()
    {
    }
}
