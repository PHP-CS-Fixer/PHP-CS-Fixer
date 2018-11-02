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

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\NoUselessCommentFixer
 */
final class NoUselessCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestCases()
    {
        yield [
            '<?php
            /**
             */
            class Foo {}
             ',
            '<?php
            /**
             * Class Foo.
             */
            class Foo {}
             ',
        ];

        yield [
            '<?php
            /**
             */
            class Bar {}
             ',
            '<?php
            /**
             * Class Foo\Bar.
             */
            class Bar {}
             ',
        ];

        yield [
            '<?php
            /**
             */
            class Foo {}
             ',
            '<?php
            /**
             * Class Foo
             */
            class Foo {}
             ',
        ];

        yield [
            '<?php
            /**
             */
            interface Foo {}
             ',
            '<?php
            /**
             * Interface Foo
             */
            interface Foo {}
             ',
        ];

        yield [
            '<?php
            /**
             */
            trait Foo {}
             ',
            '<?php
            /**
             * Trait Foo
             */
            trait Foo {}
             ',
        ];

        yield [
            '<?php
            /**
             *
             * Class provides nice functionality
             */
            class Foo {}
             ',
            '<?php
            /**
             * Class Foo.
             *
             * Class provides nice functionality
             */
            class Foo {}
             ',
        ];

        yield [
            '<?php
            /**
             * Class provides nice functionality
             *
             */
            class Foo {}
             ',
            '<?php
            /**
             * Class provides nice functionality
             *
             * Class Foo.
             */
            class Foo {}
             ',
        ];

        yield [
            '<?php
            /**
             * @author John Doe
             * Class is cool
             */
            class Foo {}
             ',
            '<?php
            /**
             * @author John Doe
             * Class Foo.
             * Class is cool
             */
            class Foo {}
             ',
        ];

        yield [
            '<?php
            /** @see example.com
             */
            abstract class Foo {}
             ',
            '<?php
            /** Class Foo
             * @see example.com
             */
            abstract class Foo {}
             ',
        ];

        yield [
            '<?php
            //
            // Class that does something
            final class Foo {}
             ',
            '<?php
            // Class Foo
            // Class that does something
            final class Foo {}
             ',
        ];

        yield [
            '<?php
            #
            # Class that does something
            final class Foo {}
             ',
            '<?php
            # Class Foo
            # Class that does something
            final class Foo {}
             ',
        ];

        yield [
            '<?php
            // I am class Foo
            class Foo {}
             ',
        ];

        yield [
            '<?php
            // Class Foo
            if (true) {
                return false;
            }
             ',
        ];

        yield [
            '<?php
             /**
              * @coversDefaultClass CoveredClass
              */
             class Foo {}
             ',
        ];

        yield [
            '<?php
             /**
              * @coversDefaultClass ClassCovered
              */
             class Foo {}
             ',
        ];

        yield [
            '<?php
             class Foo {
                 /**
                  */
                 public function __constructor() {}
             }
             ',
            '<?php
             class Foo {
                 /**
                  * Foo constructor.
                  */
                 public function __constructor() {}
             }
             ',
        ];

        yield [
            '<?php
             class Foo {
                 /**
                  */
                 public function setA() {}
                 /**
                  */
                 public static function setB() {}
                 /**
                  */
                 static public function setC() {}
                 /**
                  */
                 protected function setD() {}
                 /**
                  */
                 private function setE() {}
                 /**
                  */
                 private function getF() {}
                 /**
                  */
                 private function setG() {}
                 /**
                  */
                 private function getH() {}
                 /**
                  * Does not really gets I
                  */
                 private function getI() {}
                 /**
                  * Get J in a fancy way
                  */
                 private function getJ() {}
             }
             ',
            '<?php
             class Foo {
                 /**
                  * Set A
                  */
                 public function setA() {}
                 /**
                  * Set B.
                  */
                 public static function setB() {}
                 /**
                  * Set C
                  */
                 static public function setC() {}
                 /**
                  * Set D
                  */
                 protected function setD() {}
                 /**
                  * Sets E
                  */
                 private function setE() {}
                 /**
                  * Get F
                  */
                 private function getF() {}
                 /**
                  * Gets G
                  */
                 private function setG() {}
                 /**
                  * Gets H.
                  */
                 private function getH() {}
                 /**
                  * Does not really gets I
                  */
                 private function getI() {}
                 /**
                  * Get J in a fancy way
                  */
                 private function getJ() {}
             }
             ',
        ];
    }
}
