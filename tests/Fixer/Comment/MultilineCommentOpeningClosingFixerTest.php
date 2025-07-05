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

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer>
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class MultilineCommentOpeningClosingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php /** Opening DocBlock */'];

        yield [
            '<?php /* Opening comment */',
            '<?php /*** Opening comment */',
        ];

        yield [
            '<?php /*\ Opening false-DocBlock */',
            '<?php /**\ Opening false-DocBlock */',
        ];

        yield [
            '<?php /** Closing DocBlock */',
            '<?php /** Closing DocBlock ***/',
        ];

        yield [
            '<?php /* Closing comment */',
            '<?php /* Closing comment ***/',
        ];

        yield [
            '<?php /**/',
            '<?php /***/',
        ];

        yield [
            '<?php /**/',
            '<?php /********/',
        ];

        yield [
            <<<'EOT'
                <?php

                /*
                 * WUT
                 */
                EOT,
            <<<'EOT'
                <?php

                /********
                 * WUT
                 ********/
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php

                /*\
                 * False DocBlock
                 */
                EOT,
            <<<'EOT'
                <?php

                /**\
                 * False DocBlock
                 */
                EOT,
        ];

        yield [
            <<<'EOT'
                <?php
                # Hash
                #*** Hash asterisk
                // Slash
                //*** Slash asterisk

                /*
                /**
                /***
                Weird multiline comment
                */

                EOT,
        ];
    }
}
