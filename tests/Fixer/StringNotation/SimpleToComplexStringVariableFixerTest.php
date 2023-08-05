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
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\SimpleToComplexStringVariableFixer
 */
final class SimpleToComplexStringVariableFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'basic fix' => [
            <<<'EXPECTED'
                <?php
                $name = "World";
                echo "Hello {$name}!";
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                $name = "World";
                echo "Hello ${name}!";
                INPUT
            ,
        ];

        yield 'heredoc' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello {$name}!
                TEST;

                EXPECTED
            ,
            <<<'INPUT'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello ${name}!
                TEST;

                INPUT
            ,
        ];

        yield 'implicit' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo "Hello $name!";
                EXPECTED
            ,
        ];

        yield 'implicit again' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo "Hello { $name }!";
                EXPECTED
            ,
        ];

        yield 'escaped' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo "Hello \${name}";
                EXPECTED
            ,
        ];

        yield 'double dollar' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo "Hello \${$name}";
                EXPECTED
            ,
            <<<'INPUT'
                <?php
                $name = 'World';
                echo "Hello $${name}";
                INPUT
            ,
        ];

        yield 'double dollar heredoc' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello \${$name}!
                TEST;

                EXPECTED
            ,
            <<<'INPUT'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello $${name}!
                TEST;

                INPUT
            ,
        ];

        yield 'double dollar single quote' => [
            <<<'EXPECTED'
                <?php
                $name = 'World';
                echo 'Hello $${name}';
                EXPECTED
            ,
        ];
    }
}
