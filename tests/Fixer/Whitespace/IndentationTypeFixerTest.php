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
 * @covers \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class IndentationTypeFixerTest extends AbstractFixerTestCase
{
    /**
     * @param non-empty-string $indent
     * @param non-empty-string $lineEnding
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, string $indent = '    ', string $lineEnding = "\n"): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig($indent, $lineEnding));
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: string, 3?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
        echo ALPHA;',
            "<?php
\t\techo ALPHA;",
        ];

        yield [
            '<?php
        echo BRAVO;',
            "<?php
\t\techo BRAVO;",
        ];

        yield [
            '<?php
        echo CHARLIE;',
            "<?php
 \t\techo CHARLIE;",
        ];

        yield [
            '<?php
        echo DELTA;',
            "<?php
  \t\techo DELTA;",
        ];

        yield [
            "<?php
        echo 'ECHO';",
            "<?php
   \t\techo 'ECHO';",
        ];

        yield [
            '<?php
        echo FOXTROT;',
            "<?php
\t \techo FOXTROT;",
        ];

        yield [
            '<?php
        echo GOLF;',
            "<?php
\t  \techo GOLF;",
        ];

        yield [
            '<?php
        echo HOTEL;',
            "<?php
\t   \techo HOTEL;",
        ];

        yield [
            '<?php
        echo INDIA;',
            "<?php
\t    echo INDIA;",
        ];

        yield [
            '<?php
        echo JULIET;',
            "<?php
 \t   \techo JULIET;",
        ];

        yield [
            '<?php
        echo KILO;',
            "<?php
  \t  \techo KILO;",
        ];

        yield [
            '<?php
        echo MIKE;',
            "<?php
   \t \techo MIKE;",
        ];

        yield [
            '<?php
        echo NOVEMBER;',
            "<?php
    \techo NOVEMBER;",
        ];

        yield [
            '<?php
         echo OSCAR;',
            "<?php
\t \t echo OSCAR;",
        ];

        yield [
            '<?php
          echo PAPA;',
            "<?php
\t \t  echo PAPA;",
        ];

        yield [
            '<?php
           echo QUEBEC;',
            "<?php
\t \t   echo QUEBEC;",
        ];

        yield [
            '<?php $x = "a: \t";',
        ];

        yield [
            "<?php
\$x = \"
\tLike
\ta
\tdog\";",
        ];

        yield [
            '<?php
    /**
     * Test that tabs in docblocks are converted to spaces.
     *
     * @test
     *
     * @return
     */',
            "<?php
\t/**
\t * Test that tabs in docblocks are converted to spaces.
\t *
\t * @test
\t *
\t * @return
\t */",
        ];

        yield [
            '<?php
        /**
         * Test that tabs in docblocks are converted to spaces.
         */',
            "<?php
\t\t/**
\t\t * Test that tabs in docblocks are converted to spaces.
\t\t */",
        ];

        yield [
            '<?php
    /*
     | Test that tabs in comments are converted to spaces    '."\t".'.
     */',
            "<?php
\t/*
\t | Test that tabs in comments are converted to spaces    \t.
\t */",
        ];

        yield [
            "<?php
    /**
     * This variable
     * should not be '\t', really!
     */",
            "<?php
\t/**
\t * This variable
\t * should not be '\t', really!
\t */",
        ];

        yield [
            "<?php\necho 1;\n?>\r\n\t\$a = ellow;",
        ];

        foreach (self::getFixCases() as $name => $case) {
            yield 'tabs - '.$name => [...$case, "\t", "\r\n"];

            if ('mix indentation' === $name) {
                continue;
            }

            yield 'spaces - '.$name => [$case[1], $case[0], '    ', "\r\n"];
        }

        yield [
            '<?php
if (true) {
  if (true) {
    (new stdClass())->foo(
      "text",
      "text2"
    );
  }
}',
            null,
            '  ',
        ];

        yield [
            "<?php
if (true) {
  if (true) {
    (new stdClass())->foo(
      'text',
      'text2'
    );
  }
}",
            "<?php
if (true) {
  if (true) {
\t(new stdClass())->foo(
\t  'text',
\t  'text2'
\t);
  }
}",
            '  ',
        ];

        yield [
            '<?php
    /*
     * Foo
     */
',
            "<?php
\t/*
\t * Foo
\t */
",
            '  ',
        ];
    }

    /**
     * @return iterable<array{string, string}>
     */
    private static function getFixCases(): iterable
    {
        yield [
            "<?php
\t\techo KILO;",
            '<?php
        echo KILO;',
        ];

        yield [
            "<?php
\t\t   echo QUEBEC;",
            '<?php
           echo QUEBEC;',
        ];

        yield [
            "<?php
\t/**
\t * This variable
\t * should not be '\t', really!
\t */",
            "<?php
    /**
     * This variable
     * should not be '\t', really!
     */",
        ];

        yield 'mix indentation' => [
            "<?php
\t\t/*
\t\t * multiple indentation
\t\t * shall be handled properly
\t\t */",
            "<?php
\t\t/*
\t\t * multiple indentation
    \t * shall be handled properly
\t     */",
        ];

        yield 'do not touch whitespace that is not indentation' => [
            "<?php
function myFunction() {
\t\$foo        = 1;
\t//abc
\t\$myFunction = 2;
\t\$middleVar  = 1;
}",
            '<?php
function myFunction() {
    $foo        = 1;
    //abc
    $myFunction = 2;
    $middleVar  = 1;
}',
        ];
    }
}
