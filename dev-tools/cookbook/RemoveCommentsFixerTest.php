<?php

// tests/Fixer/Comment/RemoveCommentsFixerTest.php

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
 * @author Your name <your@email.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\RemoveCommentsFixer
 */
final class RemoveCommentsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    // ...
    public static function provideFixCases()
    {
        return [
            [
                '<?php echo "This should be changed"; ', // This is expected output
                '<?php echo "This should be changed"; /* Comment */', // This is input
            ],
        ];
    }

    public static function provideEmptyCases()
    {
        return [];
    }

    public static function provideNoChangeCases()
    {
        return [
            ['<?php echo "This should not be changed";'], // Each sub-array is a test
        ];
    }
    // ...
}
