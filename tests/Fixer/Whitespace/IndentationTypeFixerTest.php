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

    public static function provideFixCases(): array
    {
        $cases = [];

        $cases[] = [
            '<?php
        echo ALPHA;',
            "<?php
\t\techo ALPHA;",
        ];

        $cases[] = [
            '<?php
        echo BRAVO;',
            "<?php
\t\techo BRAVO;",
        ];

        $cases[] = [
            '<?php
        echo CHARLIE;',
            "<?php
 \t\techo CHARLIE;",
        ];

        $cases[] = [
            '<?php
        echo DELTA;',
            "<?php
  \t\techo DELTA;",
        ];

        $cases[] = [
            "<?php
        echo 'ECHO';",
            "<?php
   \t\techo 'ECHO';",
        ];

        $cases[] = [
            '<?php
        echo FOXTROT;',
            "<?php
\t \techo FOXTROT;",
        ];

        $cases[] = [
            '<?php
        echo GOLF;',
            "<?php
\t  \techo GOLF;",
        ];

        $cases[] = [
            '<?php
        echo HOTEL;',
            "<?php
\t   \techo HOTEL;",
        ];

        $cases[] = [
            '<?php
        echo INDIA;',
            "<?php
\t    echo INDIA;",
        ];

        $cases[] = [
            '<?php
        echo JULIET;',
            "<?php
 \t   \techo JULIET;",
        ];

        $cases[] = [
            '<?php
        echo KILO;',
            "<?php
  \t  \techo KILO;",
        ];

        $cases[] = [
            '<?php
        echo MIKE;',
            "<?php
   \t \techo MIKE;",
        ];

        $cases[] = [
            '<?php
        echo NOVEMBER;',
            "<?php
    \techo NOVEMBER;",
        ];

        $cases[] = [
            '<?php
         echo OSCAR;',
            "<?php
\t \t echo OSCAR;",
        ];

        $cases[] = [
            '<?php
          echo PAPA;',
            "<?php
\t \t  echo PAPA;",
        ];

        $cases[] = [
            '<?php
           echo QUEBEC;',
            "<?php
\t \t   echo QUEBEC;",
        ];

        $cases[] = [
            '<?php $x = "a: \t";',
        ];

        $cases[] = [
            "<?php
\$x = \"
\tLike
\ta
\tdog\";",
        ];

        $cases[] = [
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

        $cases[] = [
            '<?php
        /**
         * Test that tabs in docblocks are converted to spaces.
         */',
            "<?php
\t\t/**
\t\t * Test that tabs in docblocks are converted to spaces.
\t\t */",
        ];

        $cases[] = [
            '<?php
    /*
     | Test that tabs in comments are converted to spaces    '."\t".'.
     */',
            "<?php
\t/*
\t | Test that tabs in comments are converted to spaces    \t.
\t */",
        ];

        $cases[] = [
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

        $cases[] = [
            "<?php\necho 1;\n?>\r\n\t\$a = ellow;",
        ];

        return $cases;
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): array
    {
        $cases = [];

        $cases[] = [
            "<?php
\t\techo KILO;",
            '<?php
        echo KILO;',
        ];

        $cases[] = [
            "<?php
\t\t   echo QUEBEC;",
            '<?php
           echo QUEBEC;',
        ];

        $cases[] = [
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

        $cases['mix indentation'] = [
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

        $cases[] = [
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

        return $cases;
    }

    /**
     * @dataProvider provideMessyWhitespacesReversedCases
     */
    public function testMessyWhitespacesReversed(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));

        $this->doTest($input, $expected);
    }

    public static function provideMessyWhitespacesReversedCases(): array
    {
        return array_filter(
            self::provideMessyWhitespacesCases(),
            static function (string $key): bool {
                return !str_contains($key, 'mix indentation');
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @dataProvider provideDoubleSpaceIndentCases
     */
    public function testDoubleSpaceIndent(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('  '));

        $this->doTest($expected, $input);
    }

    public static function provideDoubleSpaceIndentCases(): array
    {
        return [
            ['<?php
if (true) {
  if (true) {
    (new stdClass())->foo(
      "text",
      "text2"
    );
  }
}'],
            [
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
            ],
            [
                '<?php
    /*
     * Foo
     */
',

                "<?php
\t/*
\t * Foo
\t */
", ],
        ];
    }
}
