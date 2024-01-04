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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Marcel Behrmann <marcel@behrmann.dev>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\PhpdocReadonlyClassCommentToKeywordFixer
 */
final class PhpdocReadonlyClassCommentToKeywordFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @requires PHP 8.2
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php


                readonly class C {
                }
                EOD,
            <<<'EOD'
                <?php

                /** @readonly */
                class C {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                /**
                 */
                readonly class C {
                }
                EOD,
            <<<'EOD'
                <?php

                /**
                 * @readonly
                 */
                class C {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                /**
                 * Very impressive class
                 *
                 */
                readonly class C {
                }
                EOD,
            <<<'EOD'
                <?php

                /**
                 * Very impressive class
                 *
                 * @readonly
                 */
                class C {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                /**
                 */
                final readonly class C {
                }
                EOD,
            <<<'EOD'
                <?php

                /**
                 * @readonly
                 */
                final class C {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                /**
                 */
                abstract readonly class C {
                }
                EOD,
            <<<'EOD'
                <?php

                /**
                 * @readonly
                 */
                abstract class C {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 */
                readonly class A {
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @readonly
                 */
                readonly class A {
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /** Class A. */
                class A {
                }
                EOD,
        ];
    }
}
