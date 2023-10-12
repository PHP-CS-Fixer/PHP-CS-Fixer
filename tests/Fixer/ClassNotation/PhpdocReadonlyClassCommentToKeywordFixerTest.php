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
            <<<'EOT'
                <?php


                readonly class C {
                }
                EOT,
            <<<'EOT'
                <?php

                /** @readonly */
                class C {
                }
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php

                /**
                 */
                readonly class C {
                }
                EOT,
            <<<'EOT'
                <?php

                /**
                 * @readonly
                 */
                class C {
                }
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php

                /**
                 * Very impressive class
                 *
                 */
                readonly class C {
                }
                EOT,
            <<<'EOT'
                <?php

                /**
                 * Very impressive class
                 *
                 * @readonly
                 */
                class C {
                }
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php

                /**
                 */
                final readonly class C {
                }
                EOT,
            <<<'EOT'
                <?php

                /**
                 * @readonly
                 */
                final class C {
                }
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php

                /**
                 */
                abstract readonly class C {
                }
                EOT,
            <<<'EOT'
                <?php

                /**
                 * @readonly
                 */
                abstract class C {
                }
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php
                /**
                 */
                readonly class A {
                }
                EOT,
            <<<'EOT'
                <?php
                /**
                 * @readonly
                 */
                readonly class A {
                }
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php
                /** Class A. */
                class A {
                }
                EOT,
        ];
    }
}
