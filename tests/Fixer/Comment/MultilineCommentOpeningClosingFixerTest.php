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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDocblocksCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDocblocksCases()
    {
        return [
            ['<?php /** Opening DocBlock */'],
            [
                '<?php /* Opening Multiline comment */',
                '<?php /*** Opening Multiline comment */',
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
                '<?php /* Closing Multiline comment */',
                '<?php /* Closing Multiline comment ***/',
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
