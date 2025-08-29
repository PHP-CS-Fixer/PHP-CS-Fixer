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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\NoBinaryStringFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\StringNotation\NoBinaryStringFixer>
 *
 * @author ntzm
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoBinaryStringFixerTest extends AbstractFixerTestCase
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
        yield [
            '<?php echo \'hello world\';',
            '<?php echo b\'hello world\';',
        ];

        yield [
            '<?php $a=\'hello world\';',
            '<?php $a=b\'hello world\';',
        ];

        yield [
            '<?php echo (\'hello world\');',
            '<?php echo (b\'hello world\');',
        ];

        yield [
            '<?php echo "hi".\'hello world\';',
            '<?php echo "hi".b\'hello world\';',
        ];

        yield [
            '<?php echo "hello world";',
            '<?php echo b"hello world";',
        ];

        yield [
            '<?php echo \'hello world\';',
            '<?php echo B\'hello world\';',
        ];

        yield [
            '<?php echo "hello world";',
            '<?php echo B"hello world";',
        ];

        yield [
            '<?php echo /* foo */"hello world";',
            '<?php echo /* foo */B"hello world";',
        ];

        yield [
            "<?php echo <<<EOT\nfoo\nEOT;\n",
            "<?php echo b<<<EOT\nfoo\nEOT;\n",
        ];

        yield [
            "<?php echo <<<EOT\nfoo\nEOT;\n",
            "<?php echo B<<<EOT\nfoo\nEOT;\n",
        ];

        yield [
            "<?php echo <<<'EOT'\nfoo\nEOT;\n",
            "<?php echo b<<<'EOT'\nfoo\nEOT;\n",
        ];

        yield [
            "<?php echo <<<'EOT'\nfoo\nEOT;\n",
            "<?php echo B<<<'EOT'\nfoo\nEOT;\n",
        ];

        yield [
            "<?php echo <<<\"EOT\"\nfoo\nEOT;\n",
            "<?php echo b<<<\"EOT\"\nfoo\nEOT;\n",
        ];

        yield [
            "<?php echo <<<\"EOT\"\nfoo\nEOT;\n",
            "<?php echo B<<<\"EOT\"\nfoo\nEOT;\n",
        ];

        yield [
            '<?php
                    echo "{$fruit}";
                    echo " {$fruit}";
                ',
            '<?php
                    echo b"{$fruit}";
                    echo b" {$fruit}";
                ',
        ];

        yield ['<?php echo Bar::foo();'];

        yield ['<?php echo bar::foo();'];

        yield ['<?php echo "b";'];

        yield ['<?php echo b;'];

        yield ['<?php echo b."a";'];

        yield ['<?php echo b("a");'];
    }
}
