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
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } else {
                                    bar();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                else {
                                    bar();
                                }
                EOD,
        ];

        yield 'elseif (same line, default)' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } elseif ($bar) {
                                    bar();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                elseif ($bar) {
                                    bar();
                                }
                EOD,
        ];

        yield 'else if (same line, default)' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } else if ($bar) {
                                    bar();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                else if ($bar) {
                                    bar();
                                }
                EOD,
        ];

        yield 'do while (same line, default)' => [
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                } while ($foo);
                EOD,
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                }
                                while ($foo);
                EOD,
        ];

        yield 'try catch finally (same line, default)' => [
            <<<'EOD'
                <?php
                                try {
                                    foo();
                                } catch (Throwable $e) {
                                    bar();
                                } finally {
                                    baz();
                                }
                EOD,
            <<<'EOD'
                <?php
                                try {
                                    foo();
                                }
                                catch (Throwable $e) {
                                    bar();
                                }
                                finally {
                                    baz();
                                }
                EOD,
        ];

        yield 'else (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                else {
                                    bar();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } else {
                                    bar();
                                }
                EOD,
            ['position' => 'next_line'],
        ];

        yield 'elseif (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                elseif ($bar) {
                                    bar();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } elseif ($bar) {
                                    bar();
                                }
                EOD,
            ['position' => 'next_line'],
        ];

        yield 'else if (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                else if ($bar) {
                                    bar();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } else if ($bar) {
                                    bar();
                                }
                EOD,
            ['position' => 'next_line'],
        ];

        yield 'do while (next line)' => [
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                }
                                while ($foo);
                EOD,
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                } while ($foo);
                EOD,
            ['position' => 'next_line'],
        ];

        yield 'try catch finally (next line)' => [
            <<<'EOD'
                <?php
                                try {
                                    foo();
                                }
                                catch (Throwable $e) {
                                    bar();
                                }
                                finally {
                                    baz();
                                }
                EOD,
            <<<'EOD'
                <?php
                                try {
                                    foo();
                                } catch (Throwable $e) {
                                    bar();
                                } finally {
                                    baz();
                                }
                EOD,
            ['position' => 'next_line'],
        ];

        yield 'else with comment after closing brace' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } // comment
                                else {
                                    bar();
                                }
                EOD,
        ];

        yield 'elseif with comment after closing brace' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } // comment
                                elseif ($bar) {
                                    bar();
                                }
                EOD,
        ];

        yield 'else if with comment after closing brace' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                } // comment
                                else if ($bar) {
                                    bar();
                                }
                EOD,
        ];

        yield 'do while with comment after closing brace' => [
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                } // comment
                                while (false);
                EOD,
        ];

        yield 'try catch finally with comment after closing brace' => [
            <<<'EOD'
                <?php
                                try {
                                    foo();
                                } // comment
                                catch (Throwable $e) {
                                    bar();
                                } // comment
                                finally {
                                    baz();
                                }
                EOD,
        ];

        yield 'while not after do' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                                while ($bar) {
                                    bar();
                                }
                EOD,
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
