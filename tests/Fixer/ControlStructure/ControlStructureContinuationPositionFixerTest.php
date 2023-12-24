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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Preg;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\ControlStructureContinuationPositionFixer
 */
final class ControlStructureContinuationPositionFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'else (same line, default)' => [
            '<?php
                if ($foo) {
                    foo();
                } else {
                    bar();
                }',
            '<?php
                if ($foo) {
                    foo();
                }
                else {
                    bar();
                }',
        ];

        yield 'elseif (same line, default)' => [
            '<?php
                if ($foo) {
                    foo();
                } elseif ($bar) {
                    bar();
                }',
            '<?php
                if ($foo) {
                    foo();
                }
                elseif ($bar) {
                    bar();
                }',
        ];

        yield 'else if (same line, default)' => [
            '<?php
                if ($foo) {
                    foo();
                } else if ($bar) {
                    bar();
                }',
            '<?php
                if ($foo) {
                    foo();
                }
                else if ($bar) {
                    bar();
                }',
        ];

        yield 'do while (same line, default)' => [
            '<?php
                do {
                    foo();
                } while ($foo);',
            '<?php
                do {
                    foo();
                }
                while ($foo);',
        ];

        yield 'try catch finally (same line, default)' => [
            '<?php
                try {
                    foo();
                } catch (Throwable $e) {
                    bar();
                } finally {
                    baz();
                }',
            '<?php
                try {
                    foo();
                }
                catch (Throwable $e) {
                    bar();
                }
                finally {
                    baz();
                }',
        ];

        yield 'else (next line)' => [
            '<?php
                if ($foo) {
                    foo();
                }
                else {
                    bar();
                }',
            '<?php
                if ($foo) {
                    foo();
                } else {
                    bar();
                }',
            ['position' => 'next_line'],
        ];

        yield 'elseif (next line)' => [
            '<?php
                if ($foo) {
                    foo();
                }
                elseif ($bar) {
                    bar();
                }',
            '<?php
                if ($foo) {
                    foo();
                } elseif ($bar) {
                    bar();
                }',
            ['position' => 'next_line'],
        ];

        yield 'else if (next line)' => [
            '<?php
                if ($foo) {
                    foo();
                }
                else if ($bar) {
                    bar();
                }',
            '<?php
                if ($foo) {
                    foo();
                } else if ($bar) {
                    bar();
                }',
            ['position' => 'next_line'],
        ];

        yield 'do while (next line)' => [
            '<?php
                do {
                    foo();
                }
                while ($foo);',
            '<?php
                do {
                    foo();
                } while ($foo);',
            ['position' => 'next_line'],
        ];

        yield 'try catch finally (next line)' => [
            '<?php
                try {
                    foo();
                }
                catch (Throwable $e) {
                    bar();
                }
                finally {
                    baz();
                }',
            '<?php
                try {
                    foo();
                } catch (Throwable $e) {
                    bar();
                } finally {
                    baz();
                }',
            ['position' => 'next_line'],
        ];

        yield 'else with comment after closing brace' => [
            '<?php
                if ($foo) {
                    foo();
                } // comment
                else {
                    bar();
                }',
        ];

        yield 'elseif with comment after closing brace' => [
            '<?php
                if ($foo) {
                    foo();
                } // comment
                elseif ($bar) {
                    bar();
                }',
        ];

        yield 'else if with comment after closing brace' => [
            '<?php
                if ($foo) {
                    foo();
                } // comment
                else if ($bar) {
                    bar();
                }',
        ];

        yield 'do while with comment after closing brace' => [
            '<?php
                do {
                    foo();
                } // comment
                while (false);',
        ];

        yield 'try catch finally with comment after closing brace' => [
            '<?php
                try {
                    foo();
                } // comment
                catch (Throwable $e) {
                    bar();
                } // comment
                finally {
                    baz();
                }',
        ];

        yield 'while not after do' => [
            '<?php
                if ($foo) {
                    foo();
                }
                while ($bar) {
                    bar();
                }',
        ];
    }

    /**
     * @param null|array<string, mixed> $configuration
     *
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null, array $configuration = null): void
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->fixer->setWhitespacesConfig(
            new WhitespacesFixerConfig('    ', "\r\n")
        );

        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        foreach (self::provideFixCases() as $label => $case) {
            yield $label => [
                Preg::replace('/\n/', "\r\n", $case[0]),
                isset($case[1]) ? Preg::replace('/\n/', "\r\n", $case[1]) : null,
                $case[2] ?? null,
            ];
        }
    }
}
