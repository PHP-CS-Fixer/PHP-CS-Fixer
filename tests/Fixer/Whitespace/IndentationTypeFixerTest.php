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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer
 */
final class IndentationTypeFixerTest extends AbstractFixerTestCase
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
        yield [
            <<<'EOD'
                <?php
                        echo ALPHA;
                EOD,
            <<<EOD
                <?php
                \t\techo ALPHA;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo BRAVO;
                EOD,
            <<<EOD
                <?php
                \t\techo BRAVO;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo CHARLIE;
                EOD,
            <<<EOD
                <?php
                 \t\techo CHARLIE;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo DELTA;
                EOD,
            <<<EOD
                <?php
                  \t\techo DELTA;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo 'ECHO';
                EOD,
            <<<EOD
                <?php
                   \t\techo 'ECHO';
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo FOXTROT;
                EOD,
            <<<EOD
                <?php
                \t \techo FOXTROT;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo GOLF;
                EOD,
            <<<EOD
                <?php
                \t  \techo GOLF;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo HOTEL;
                EOD,
            <<<EOD
                <?php
                \t   \techo HOTEL;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo INDIA;
                EOD,
            <<<EOD
                <?php
                \t    echo INDIA;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo JULIET;
                EOD,
            <<<EOD
                <?php
                 \t   \techo JULIET;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo KILO;
                EOD,
            <<<EOD
                <?php
                  \t  \techo KILO;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo MIKE;
                EOD,
            <<<EOD
                <?php
                   \t \techo MIKE;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        echo NOVEMBER;
                EOD,
            <<<EOD
                <?php
                    \techo NOVEMBER;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                         echo OSCAR;
                EOD,
            <<<EOD
                <?php
                \t \t echo OSCAR;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                          echo PAPA;
                EOD,
            <<<EOD
                <?php
                \t \t  echo PAPA;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                           echo QUEBEC;
                EOD,
            <<<EOD
                <?php
                \t \t   echo QUEBEC;
                EOD,
        ];

        yield [
            '<?php $x = "a: \t";',
        ];

        yield [
            <<<EOD
                <?php
                \$x = "
                \tLike
                \ta
                \tdog";
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /**
                     * Test that tabs in docblocks are converted to spaces.
                     *
                     * @test
                     *
                     * @return
                     */
                EOD,
            <<<EOD
                <?php
                \t/**
                \t * Test that tabs in docblocks are converted to spaces.
                \t *
                \t * @test
                \t *
                \t * @return
                \t */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                        /**
                         * Test that tabs in docblocks are converted to spaces.
                         */
                EOD,
            <<<EOD
                <?php
                \t\t/**
                \t\t * Test that tabs in docblocks are converted to spaces.
                \t\t */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     | Test that tabs in comments are converted to spaces
                EOD.'    '."\t".<<<'EOD'
                .
                     */
                EOD,
            <<<EOD
                <?php
                \t/*
                \t | Test that tabs in comments are converted to spaces    \t.
                \t */
                EOD,
        ];

        yield [
            <<<EOD
                <?php
                    /**
                     * This variable
                     * should not be '\t', really!
                     */
                EOD,
            <<<EOD
                <?php
                \t/**
                \t * This variable
                \t * should not be '\t', really!
                \t */
                EOD,
        ];

        yield [
            "<?php\necho 1;\n?>\r\n\t\$a = ellow;",
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            <<<EOD
                <?php
                \t\techo KILO;
                EOD,
            <<<'EOD'
                <?php
                        echo KILO;
                EOD,
        ];

        yield [
            <<<EOD
                <?php
                \t\t   echo QUEBEC;
                EOD,
            <<<'EOD'
                <?php
                           echo QUEBEC;
                EOD,
        ];

        yield [
            <<<EOD
                <?php
                \t/**
                \t * This variable
                \t * should not be '\t', really!
                \t */
                EOD,
            <<<EOD
                <?php
                    /**
                     * This variable
                     * should not be '\t', really!
                     */
                EOD,
        ];

        yield 'mix indentation' => [
            <<<EOD
                <?php
                \t\t/*
                \t\t * multiple indentation
                \t\t * shall be handled properly
                \t\t */
                EOD,
            <<<EOD
                <?php
                \t\t/*
                \t\t * multiple indentation
                    \t * shall be handled properly
                \t     */
                EOD,
        ];

        yield [
            <<<EOD
                <?php
                function myFunction() {
                \t\$foo        = 1;
                \t//abc
                \t\$myFunction = 2;
                \t\$middleVar  = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                function myFunction() {
                    $foo        = 1;
                    //abc
                    $myFunction = 2;
                    $middleVar  = 1;
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesReversedCases
     */
    public function testMessyWhitespacesReversed(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));

        $this->doTest($input, $expected);
    }

    public static function provideMessyWhitespacesReversedCases(): iterable
    {
        foreach (self::provideMessyWhitespacesCases() as $name => $case) {
            if ('mix indentation' === $name) {
                continue;
            }

            yield $name => $case;
        }
    }

    /**
     * @dataProvider provideDoubleSpaceIndentCases
     */
    public function testDoubleSpaceIndent(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('  '));

        $this->doTest($expected, $input);
    }

    public static function provideDoubleSpaceIndentCases(): iterable
    {
        yield [<<<'EOD'
            <?php
            if (true) {
              if (true) {
                (new stdClass())->foo(
                  "text",
                  "text2"
                );
              }
            }
            EOD];

        yield [
            <<<'EOD'
                <?php
                if (true) {
                  if (true) {
                    (new stdClass())->foo(
                      'text',
                      'text2'
                    );
                  }
                }
                EOD,
            <<<EOD
                <?php
                if (true) {
                  if (true) {
                \t(new stdClass())->foo(
                \t  'text',
                \t  'text2'
                \t);
                  }
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * Foo
                     */

                EOD,

            <<<EOD
                <?php
                \t/*
                \t * Foo
                \t */

                EOD, ];
    }
}
