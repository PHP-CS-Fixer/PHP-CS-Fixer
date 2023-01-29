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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer
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

    public static function provideFixCases(): array
    {
        return [
            ['<?php /** Opening DocBlock */'],
            [
                '<?php /* Opening comment */',
                '<?php /*** Opening comment */',
            ],
            [
                '<?php /*\ Opening false-DocBlock */',
                '<?php /**\ Opening false-DocBlock */',
            ],
            [
                '<?php /** Closing DocBlock */',
                '<?php /** Closing DocBlock ***/',
            ],
            [
                '<?php /* Closing comment */',
                '<?php /* Closing comment ***/',
            ],
            [
                '<?php /**/',
                '<?php /***/',
            ],
            [
                '<?php /**/',
                '<?php /********/',
            ],
            [
                <<<'EOT'
<?php

/*
 * WUT
 */
EOT
                ,
                <<<'EOT'
<?php

/********
 * WUT
 ********/
EOT
                ,
            ],
            [
                <<<'EOT'
<?php

/*\
 * False DocBlock
 */
EOT
                ,
                <<<'EOT'
<?php

/**\
 * False DocBlock
 */
EOT
                ,
            ],
            [
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

EOT
                ,
            ],
        ];
    }
}
