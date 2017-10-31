<?php

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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesReversedCases
     */
    public function testMessyWhitespacesReversed($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));

        $this->doTest($input, $expected);
    }

    public function provideMessyWhitespacesReversedCases()
    {
        $filteredCases = [];
        $cases = $this->provideMessyWhitespacesCases();

        foreach ($cases as $key => $case) { // TODO on 5.6 bump use array_filter with ARRAY_FILTER_USE_KEY
            if (!is_string($key) || false === strpos($key, 'mix indentation')) {
                $filteredCases[] = $case;
            }
        }

        return $filteredCases;
    }
}
