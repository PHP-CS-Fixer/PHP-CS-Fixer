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

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class IndentationFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideIndentationOnly
     */
    public function testIndentationOnly($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideIndentationAndAlignment
     */
    public function testIndentationAndAlignment($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideTabInString
     */
    public function testTabInString($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @dataProvider provideTabInComment
     */
    public function testTabInComment($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideIndentationOnly()
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

        return $cases;
    }

    public function provideIndentationAndAlignment()
    {
        $cases = array();

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

        return $cases;
    }

    public function provideTabInString()
    {
        return array(
            array(
                '<?php $x = "a: \t";',
            ),
            array(
                "<?php
\$x = \"
\tLike
\ta
\tdog\";",
            ),
        );
    }

    public function provideTabInComment()
    {
        $cases = array();

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
     | Test that tabs in comments are converted to spaces.
     */',
            "<?php
\t/*
\t | Test that tabs in comments are converted to spaces.
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

        return $cases;
    }

    /**
     * @dataProvider provideTabInInlineHTML
     */
    public function testTabInInlineHTML($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideTabInInlineHTML()
    {
        $cases = array(
            array(
                "<?php\necho 1;\n?>\r\n\t\$a = ellow;",
            ),
        );

        return $cases;
    }
}
