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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer>
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer
 */
final class DeclareParenthesesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'spaces around parentheses' => [
            '<?php declare(strict_types = 1);',
            '<?php declare ( strict_types = 1 );',
        ];

        yield 'newlines (\n) around parentheses' => [
            '<?php declare(strict_types = 1);',
            '<?php declare
            (
                strict_types = 1
            );',
        ];

        yield 'newlines (\r\n) around parentheses' => [
            '<?php declare(strict_types = 1);',
            "<?php declare\r
            (\r
                strict_types = 1\r
            );",
        ];
    }
}
