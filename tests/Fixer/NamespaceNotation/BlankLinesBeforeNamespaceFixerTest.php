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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer
 */
final class BlankLinesBeforeNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(
        string $expected,
        ?string $input = null,
        ?array $config = [],
        ?WhitespacesFixerConfig $whitespaces = null
    ): void {
        if (null !== $whitespaces) {
            $this->fixer->setWhitespacesConfig($whitespaces);
        }
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'multiple blank lines between namespace declaration and PHP opening tag' => [
            "<?php\n\n\n\nnamespace X;",
            "<?php\nnamespace X;",
            ['min_line_breaks' => 4, 'max_line_breaks' => 4],
        ];

        yield 'multiple blank lines between namespace declaration and comment' => [
            "<?php\n/* Foo */\n\n\nnamespace X;",
            "<?php\n/* Foo */\nnamespace X;",
            ['min_line_breaks' => 3, 'max_line_breaks' => 3],
        ];

        yield 'multiple blank lines within min and max line breaks range' => [
            "<?php\n\n\n\nnamespace X;",
            null,
            ['min_line_breaks' => 3, 'max_line_breaks' => 5],
        ];

        yield 'multiple blank lines with fewer line breaks than minimum' => [
            "<?php\n\n\nnamespace X;",
            "<?php\n\nnamespace X;",
            ['min_line_breaks' => 3, 'max_line_breaks' => 5],
        ];

        yield 'multiple blank lines with more line breaks than maximum' => [
            "<?php\n\n\nnamespace X;",
            "<?php\n\n\n\n\nnamespace X;",
            ['min_line_breaks' => 1, 'max_line_breaks' => 3],
        ];

        yield 'enforce namespace at the same line as opening tag' => [
            '<?php namespace X;',
            "<?php\n\n\n\n\nnamespace X;",
            ['min_line_breaks' => 0, 'max_line_breaks' => 0],
        ];
    }

    /**
     * @dataProvider provideMinMaxConfigurationCases
     */
    public function testMinMaxConfiguration(int $min, int $max, bool $valid): void
    {
        if (true === $valid) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(InvalidFixerConfigurationException::class);
        }

        $fixer = new BlankLinesBeforeNamespaceFixer();
        $fixer->configure(['min_line_breaks' => $min, 'max_line_breaks' => $max]);
    }

    /**
     * @return iterable<array{int, int, bool}>
     */
    public static function provideMinMaxConfigurationCases(): iterable
    {
        yield 'same min and max' => [2, 2, true];

        yield 'correct min and max range' => [2, 4, true];

        yield 'min higher than max' => [4, 2, false];

        yield 'min lower than 0' => [-2, 2, false];

        yield 'max lower than 0' => [-4, -2, false];
    }
}
