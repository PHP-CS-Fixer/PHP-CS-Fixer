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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\CurlyBracesPositionFixer
 */
final class CurlyBracesPositionFixerTest extends AbstractFixerTestCase
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
        yield 'if (default)' => [
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo)
                                {
                                    foo();
                                }
                EOD,
        ];

        yield 'if (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo)
                                {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo) {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'else (default)' => [
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
                                if ($foo)
                                {
                                    foo();
                                }
                                else
                                {
                                    bar();
                                }
                EOD,
        ];

        yield 'else (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo)
                                {
                                    foo();
                                }
                                else
                                {
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
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'elseif (default)' => [
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
                                if ($foo)
                                {
                                    foo();
                                }
                                elseif ($bar)
                                {
                                    bar();
                                }
                EOD,
        ];

        yield 'elseif (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo)
                                {
                                    foo();
                                }
                                elseif ($bar)
                                {
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
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'else if (default)' => [
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
                                if ($foo)
                                {
                                    foo();
                                }
                                else if ($bar)
                                {
                                    bar();
                                }
                EOD,
        ];

        yield 'else if (next line)' => [
            <<<'EOD'
                <?php
                                if ($foo)
                                {
                                    foo();
                                }
                                else if ($bar)
                                {
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
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'for (default)' => [
            <<<'EOD'
                <?php
                                for (;;) {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                for (;;)
                                {
                                    foo();
                                }
                EOD,
        ];

        yield 'for (next line)' => [
            <<<'EOD'
                <?php
                                for (;;)
                                {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                for (;;) {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'foreach (default)' => [
            <<<'EOD'
                <?php
                                foreach ($foo as $bar) {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                foreach ($foo as $bar)
                                {
                                    foo();
                                }
                EOD,
        ];

        yield 'foreach (next line)' => [
            <<<'EOD'
                <?php
                                foreach ($foo as $bar)
                                {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                foreach ($foo as $bar) {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'while (default)' => [
            <<<'EOD'
                <?php
                                while ($foo) {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                while ($foo)
                                {
                                    foo();
                                }
                EOD,
        ];

        yield 'while (next line)' => [
            <<<'EOD'
                <?php
                                while ($foo)
                                {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                while ($foo) {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'do while (default)' => [
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                } while ($foo);
                EOD,
            <<<'EOD'
                <?php
                                do
                                {
                                    foo();
                                } while ($foo);
                EOD,
        ];

        yield 'do while (next line)' => [
            <<<'EOD'
                <?php
                                do
                                {
                                    foo();
                                } while ($foo);
                EOD,
            <<<'EOD'
                <?php
                                do {
                                    foo();
                                } while ($foo);
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'switch (default)' => [
            <<<'EOD'
                <?php
                                switch ($foo) {
                                    case 1:
                                        foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                switch ($foo)
                                {
                                    case 1:
                                        foo();
                                }
                EOD,
        ];

        yield 'switch (next line)' => [
            <<<'EOD'
                <?php
                                switch ($foo)
                                {
                                    case 1:
                                        foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                switch ($foo) {
                                    case 1:
                                        foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'try catch finally (default)' => [
            <<<'EOD'
                <?php
                                switch ($foo) {
                                    case 1:
                                        foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                switch ($foo)
                                {
                                    case 1:
                                        foo();
                                }
                EOD,
        ];

        yield 'try catch finally (next line)' => [
            <<<'EOD'
                <?php
                                switch ($foo)
                                {
                                    case 1:
                                        foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                switch ($foo) {
                                    case 1:
                                        foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'class (default)' => [
            <<<'EOD'
                <?php
                                class Foo
                                {
                                }
                EOD,
            <<<'EOD'
                <?php
                                class Foo {
                                }
                EOD,
        ];

        yield 'class (same line)' => [
            <<<'EOD'
                <?php
                                class Foo {
                                }
                EOD,
            <<<'EOD'
                <?php
                                class Foo
                                {
                                }
                EOD,
            ['classes_opening_brace' => 'same_line'],
        ];

        yield 'function (default)' => [
            <<<'EOD'
                <?php
                                function foo()
                                {
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo() {
                                }
                EOD,
        ];

        yield 'function (same line)' => [
            <<<'EOD'
                <?php
                                function foo() {
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo()
                                {
                                }
                EOD,
            ['functions_opening_brace' => 'same_line'],
        ];

        yield 'anonymous function (default)' => [
            <<<'EOD'
                <?php
                                $foo = function () {
                                };
                EOD,
            <<<'EOD'
                <?php
                                $foo = function ()
                                {
                                };
                EOD,
        ];

        yield 'anonymous function (next line)' => [
            <<<'EOD'
                <?php
                                $foo = function ()
                                {
                                };
                EOD,
            <<<'EOD'
                <?php
                                $foo = function () {
                                };
                EOD,
            ['anonymous_functions_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'with blank lines inside braces' => [
            <<<'EOD'
                <?php
                                class Foo
                                {

                                    public function foo()
                                    {

                                        if (true) {

                                            echo "foo";

                                        }

                                    }

                                }
                EOD,
            <<<'EOD'
                <?php
                                class Foo {

                                    public function foo() {

                                        if (true)
                                        {

                                            echo "foo";

                                        }

                                    }

                                }
                EOD,
        ];

        yield 'with comment after opening brace (default)' => [
            <<<'EOD'
                <?php
                                function foo() /* foo */ // foo
                                {
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo() /* foo */ { // foo
                                }
                EOD,
        ];

        yield 'with comment after opening brace (same line)' => [
            <<<'EOD'
                <?php
                                function foo() { // foo
                                /* foo */
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo() // foo
                                { /* foo */
                                }
                EOD,
            ['functions_opening_brace' => 'same_line'],
        ];

        yield 'next line with multiline signature' => [
            <<<'EOD'
                <?php
                                if (
                                    $foo
                                ) {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if (
                                    $foo
                                )
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with newline before closing parenthesis' => [
            <<<'EOD'
                <?php
                                if ($foo
                                ) {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if ($foo
                                )
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with newline in signature but not before closing parenthesis' => [
            <<<'EOD'
                <?php
                                if (
                                    $foo)
                                {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if (
                                    $foo) {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'anonymous class (same line)' => [
            <<<'EOD'
                <?php
                                $foo = new class() {
                                };
                EOD,
            <<<'EOD'
                <?php
                                $foo = new class()
                                {
                                };
                EOD,
        ];

        yield 'anonymous class (next line)' => [
            <<<'EOD'
                <?php
                                $foo = new class()
                                {
                                };
                EOD,
            <<<'EOD'
                <?php
                                $foo = new class() {
                                };
                EOD,
            ['anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with multiline signature and return type' => [
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): int {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): int
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with multiline signature and return type (nullable)' => [
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): ?int {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): ?int
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with multiline signature and return type (array)' => [
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): array {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): array
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with multiline signature and return type (class name)' => [
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): \Foo\Bar {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo(
                                    $foo
                                ): \Foo\Bar
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with newline before closing parenthesis and return type' => [
            <<<'EOD'
                <?php
                                function foo($foo
                                ): int {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo($foo
                                ): int
                                {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with newline before closing parenthesis and callable type' => [
            <<<'EOD'
                <?php
                                function foo($foo
                                ): callable {
                                    return function (): void {};
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo($foo
                                ): callable
                                {
                                    return function (): void {};
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'next line with newline in signature but not before closing parenthesis and return type' => [
            <<<'EOD'
                <?php
                                function foo(
                                    $foo): int
                                {
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                function foo(
                                    $foo): int {
                                    foo();
                                }
                EOD,
            ['control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end'],
        ];

        yield 'multiple elseifs' => [
            <<<'EOD'
                <?php if ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                } elseif ($foo) {
                                }
                EOD,
            <<<'EOD'
                <?php if ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                } elseif ($foo){
                                }
                EOD,
        ];

        yield 'open brace preceded by comment and whitespace' => [
            <<<'EOD'
                <?php
                                if (true) { /* foo */
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if (true) /* foo */ {
                                    foo();
                                }
                EOD,
        ];

        yield 'open brace surrounded by comment and whitespace' => [
            <<<'EOD'
                <?php
                                if (true) { /* foo */ /* bar */
                                    foo();
                                }
                EOD,
            <<<'EOD'
                <?php
                                if (true) /* foo */ { /* bar */
                                    foo();
                                }
                EOD,
        ];

        yield 'open brace not preceded by space and followed by a comment' => [
            <<<'EOD'
                <?php class test
                {
                    public function example()// example
                    {
                    }
                }

                EOD,
            <<<'EOD'
                <?php class test
                {
                    public function example(){// example
                    }
                }

                EOD,
        ];

        yield 'open brace not preceded by space and followed by a space and comment' => [
            <<<'EOD'
                <?php class test
                {
                    public function example() // example
                    {
                    }
                }

                EOD,
            <<<'EOD'
                <?php class test
                {
                    public function example(){ // example
                    }
                }

                EOD,
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'function (multiline + union return)' => [
            <<<'EOD'
                <?php
                                function sum(
                                    int|float $first,
                                    int|float $second,
                                ): int|float {
                                }
                EOD,
            <<<'EOD'
                <?php
                                function sum(
                                    int|float $first,
                                    int|float $second,
                                ): int|float
                                {
                                }
                EOD,
        ];

        yield 'function (multiline + union return with whitespace)' => [
            <<<'EOD'
                <?php
                                function sum(
                                    int|float $first,
                                    int|float $second,
                                ): int | float {
                                }
                EOD,
            <<<'EOD'
                <?php
                                function sum(
                                    int|float $first,
                                    int|float $second,
                                ): int | float
                                {
                                }
                EOD,
        ];

        yield 'method with static return type' => [
            <<<'EOD'
                <?php
                                class Foo
                                {
                                    function sum(
                                        $foo
                                    ): static {
                                    }
                                }
                EOD,
            <<<'EOD'
                <?php
                                class Foo
                                {
                                    function sum(
                                        $foo
                                    ): static
                                    {
                                    }
                                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'function (multiline + intersection return)' => [
            <<<'EOD'
                <?php
                                function foo(
                                    mixed $bar,
                                    mixed $baz,
                                ): Foo&Bar {
                                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'function (multiline + DNF return)' => [
            <<<'EOD'
                <?php
                                function foo(
                                    mixed $bar,
                                    mixed $baz,
                                ): (Foo&Bar)|int|null {
                                }
                EOD,
        ];
    }
}
