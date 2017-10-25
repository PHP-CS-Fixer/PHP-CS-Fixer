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
        $cases = array();

        $cases[] = array(
            '<?php
        echo ALPHA;',
            "<?php
\t\techo ALPHA;",
        );

        $cases[] = array(
            '<?php
        echo BRAVO;',
            "<?php
\t\techo BRAVO;",
        );

        $cases[] = array(
            '<?php
        echo CHARLIE;',
            "<?php
 \t\techo CHARLIE;",
        );

        $cases[] = array(
            '<?php
        echo DELTA;',
            "<?php
  \t\techo DELTA;",
        );

        $cases[] = array(
            "<?php
        echo 'ECHO';",
            "<?php
   \t\techo 'ECHO';",
        );

        $cases[] = array(
            '<?php
        echo FOXTROT;',
            "<?php
\t \techo FOXTROT;",
        );

        $cases[] = array(
            '<?php
        echo GOLF;',
            "<?php
\t  \techo GOLF;",
        );

        $cases[] = array(
            '<?php
        echo HOTEL;',
            "<?php
\t   \techo HOTEL;",
        );

        $cases[] = array(
            '<?php
        echo INDIA;',
            "<?php
\t    echo INDIA;",
        );

        $cases[] = array(
            '<?php
        echo JULIET;',
            "<?php
 \t   \techo JULIET;",
        );

        $cases[] = array(
            '<?php
        echo KILO;',
            "<?php
  \t  \techo KILO;",
        );

        $cases[] = array(
            '<?php
        echo MIKE;',
            "<?php
   \t \techo MIKE;",
        );

        $cases[] = array(
            '<?php
        echo NOVEMBER;',
            "<?php
    \techo NOVEMBER;",
        );

        $cases[] = array(
            '<?php
         echo OSCAR;',
            "<?php
\t \t echo OSCAR;",
        );

        $cases[] = array(
            '<?php
          echo PAPA;',
            "<?php
\t \t  echo PAPA;",
        );

        $cases[] = array(
            '<?php
           echo QUEBEC;',
            "<?php
\t \t   echo QUEBEC;",
        );

        $cases[] = array(
            '<?php $x = "a: \t";',
        );

        $cases[] = array(
            "<?php
\$x = \"
\tLike
\ta
\tdog\";",
        );

        $cases[] = array(
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
        );

        $cases[] = array(
            '<?php
        /**
         * Test that tabs in docblocks are converted to spaces.
         */',
            "<?php
\t\t/**
\t\t * Test that tabs in docblocks are converted to spaces.
\t\t */",
        );

        $cases[] = array(
            '<?php
    /*
     | Test that tabs in comments are converted to spaces    '."\t".'.
     */',
            "<?php
\t/*
\t | Test that tabs in comments are converted to spaces    \t.
\t */",
        );

        $cases[] = array(
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
        );

        $cases[] = array(
            "<?php\necho 1;\n?>\r\n\t\$a = ellow;",
        );

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
        $cases = array();

        $cases[] = array(
            "<?php
\t\techo KILO;",
            '<?php
        echo KILO;',
        );

        $cases[] = array(
            "<?php
\t\t   echo QUEBEC;",
            '<?php
           echo QUEBEC;',
        );

        $cases[] = array(
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
        );

        $cases['mix indentation'] = array(
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
        );

        $cases[] = array(
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
        );

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
        $filteredCases = array();
        $cases = $this->provideMessyWhitespacesCases();

        foreach ($cases as $key => $case) { // TODO on 5.6 bump use array_filter with ARRAY_FILTER_USE_KEY
            if (!is_string($key) || false === strpos($key, 'mix indentation')) {
                $filteredCases[] = $case;
            }
        }

        return $filteredCases;
    }
}
