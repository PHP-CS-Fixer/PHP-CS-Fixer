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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\BlankLineAfterDeclareFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Whitespace\BlankLineAfterDeclareFixer>
 *
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class BlankLineAfterDeclareFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'no whitespace' => [
            "<?php declare(strict_types=1);\n\necho 'Foo';",
            "<?php declare(strict_types=1);echo 'Foo';",
        ];

        yield 'no-newline whitespace' => [
            "<?php declare(strict_types=1);\n\necho 'Foo';",
            "<?php declare(strict_types=1); echo 'Foo';",
        ];

        yield 'single-newline whitespace' => [
            "<?php declare(strict_types=1);\n\necho 'Foo';",
            "<?php declare(strict_types=1);\necho 'Foo';",
        ];

        yield 'single-newline whitespace, braces' => [
            <<<'PHP'
                <?php declare(ticks=1) {
                    // Do stuff
                }

                echo 'Foo';
                PHP,
            <<<'PHP'
                <?php declare(ticks=1) {
                    // Do stuff
                }
                echo 'Foo';
                PHP,
        ];

        yield 'eof' => [
            "<?php declare(strict_types=1);\n\n",
            '<?php declare(strict_types=1);',
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield 'no-newline whitespace' => [
            "<?php declare(strict_types=1);\r\n\r\necho 'Foo';",
            "<?php declare(strict_types=1); echo 'Foo';",
        ];

        yield 'single-newline whitespace' => [
            "<?php declare(strict_types=1);\r\n\r\necho 'Foo';",
            "<?php declare(strict_types=1);\r\necho 'Foo';",
        ];
    }
}
