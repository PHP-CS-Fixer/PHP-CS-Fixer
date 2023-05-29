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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\NativeConstTypeDeclarationCasingFixer
 */
final class NativeConstTypeDeclarationCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.3
     */
    public function testFix(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                class Foo extends FooParent
                {
                    // fix
                    CONST int SOME_INT = 3;
                    CONST array SOME_ARRAY = [7];
                    CONST float SOME_FLOAT = 1.23;
                    CONST iterable SOME_ITERABLE = [1, 2];
                    CONST mixed SOME_MIXED = 1;
                    CONST null SOME_NULL = NULL;
                    CONST ?object SOME_OBJECT = NULL;
                    CONST ?parent SOME_PARENT = NULL;
                    CONST ?self SOME_SELF = NULL;
                    CONST string SOME_STRING = "X";

                    // do not fix
                    const INT = "A"; // INT is the name of the const, not the type
                    const FOO = 1; // no type
                }

                const INT = "A"; // INT is the name of the const, not the type
            ',
            '<?php
                class Foo extends FooParent
                {
                    // fix
                    CONST INT SOME_INT = 3;
                    CONST ARRAY SOME_ARRAY = [7];
                    CONST Float SOME_FLOAT = 1.23;
                    CONST ITERABLE SOME_ITERABLE = [1, 2];
                    CONST MIXED SOME_MIXED = 1;
                    CONST NULL SOME_NULL = NULL;
                    CONST ?OBJECT SOME_OBJECT = NULL;
                    CONST ?PARENT SOME_PARENT = NULL;
                    CONST ?Self SOME_SELF = NULL;
                    CONST STRING SOME_STRING = "X";

                    // do not fix
                    const INT = "A"; // INT is the name of the const, not the type
                    const FOO = 1; // no type
                }

                const INT = "A"; // INT is the name of the const, not the type
            ',
        ];
    }
}
