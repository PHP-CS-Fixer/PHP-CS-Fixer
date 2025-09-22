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
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordFixer>
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordFixer
 */
final class ClassKeywordFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: ?string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
                echo \PhpCsFixer\FixerDefinition\CodeSample::class;
                echo \'Foo\Bar\Baz\';
                echo \PhpCsFixer\FixerDefinition\CodeSample::class;
                echo \PhpCsFixer\FixerDefinition\CodeSample::class;
                ',
            '<?php
                echo "PhpCsFixer\FixerDefinition\CodeSample";
                echo \'Foo\Bar\Baz\';
                echo \'PhpCsFixer\FixerDefinition\CodeSample\';
                echo \'\PhpCsFixer\FixerDefinition\CodeSample\';
                ',
        ];
    }
}
