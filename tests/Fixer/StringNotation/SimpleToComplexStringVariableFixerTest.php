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
            <<<'EOD'
                <?php
                $name = "World";
                echo "Hello {$name}!";
                EOD,
            <<<'EOD'
                <?php
                $name = "World";
                echo "Hello ${name}!";
                EOD,
        ];

        yield 'heredoc' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello {$name}!
                TEST;

                EOD,
            <<<'EOD'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello ${name}!
                TEST;

                EOD,
        ];

        yield 'implicit' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo "Hello $name!";
                EOD,
        ];

        yield 'implicit again' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo "Hello { $name }!";
                EOD,
        ];

        yield 'escaped' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo "Hello \${name}";
                EOD,
        ];

        yield 'double dollar' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo "Hello \${$name}";
                EOD,
            <<<'EOD'
                <?php
                $name = 'World';
                echo "Hello $${name}";
                EOD,
        ];

        yield 'double dollar heredoc' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello \${$name}!
                TEST;

                EOD,
            <<<'EOD'
                <?php
                $name = 'World';
                echo <<<TEST
                Hello $${name}!
                TEST;

                EOD,
        ];

        yield 'double dollar single quote' => [
            <<<'EOD'
                <?php
                $name = 'World';
                echo 'Hello $${name}';
                EOD,
        ];
    }
}
