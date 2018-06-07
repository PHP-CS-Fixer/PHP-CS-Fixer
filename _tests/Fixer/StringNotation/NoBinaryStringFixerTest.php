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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\NoBinaryStringFixer
 */
final class NoBinaryStringFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
                '<?php echo \'hello world\';',
                '<?php echo b\'hello world\';',
            ],
            [
                '<?php $a=\'hello world\';',
                '<?php $a=b\'hello world\';',
            ],
            [
                '<?php echo (\'hello world\');',
                '<?php echo (b\'hello world\');',
            ],
            [
                '<?php echo "hi".\'hello world\';',
                '<?php echo "hi".b\'hello world\';',
            ],
            [
                '<?php echo "hello world";',
                '<?php echo b"hello world";',
            ],
            [
                '<?php echo \'hello world\';',
                '<?php echo B\'hello world\';',
            ],
            [
                '<?php echo "hello world";',
                '<?php echo B"hello world";',
            ],
            [
                '<?php echo "hello world";',
                '<?php echo B"hello world";',
            ],
            [
                '<?php echo /* foo */"hello world";',
                '<?php echo /* foo */B"hello world";',
            ],
            [
                "<?php echo <<<EOT\nfoo\nEOT;\n",
                "<?php echo b<<<EOT\nfoo\nEOT;\n",
            ],
            [
                "<?php echo <<<EOT\nfoo\nEOT;\n",
                "<?php echo B<<<EOT\nfoo\nEOT;\n",
            ],
            [
                "<?php echo <<<'EOT'\nfoo\nEOT;\n",
                "<?php echo b<<<'EOT'\nfoo\nEOT;\n",
            ],
            [
                "<?php echo <<<'EOT'\nfoo\nEOT;\n",
                "<?php echo B<<<'EOT'\nfoo\nEOT;\n",
            ],
            [
                "<?php echo <<<\"EOT\"\nfoo\nEOT;\n",
                "<?php echo b<<<\"EOT\"\nfoo\nEOT;\n",
            ],
            [
                "<?php echo <<<\"EOT\"\nfoo\nEOT;\n",
                "<?php echo B<<<\"EOT\"\nfoo\nEOT;\n",
            ],
            ['<?php echo Bar::foo();'],
            ['<?php echo bar::foo();'],
            ['<?php echo "b";'],
            ['<?php echo b;'],
            ['<?php echo b."a";'],
            ['<?php echo b("a");'],
        ];
    }
}
