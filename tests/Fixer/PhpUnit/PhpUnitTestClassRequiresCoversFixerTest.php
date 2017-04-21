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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer
 */
final class PhpUnitTestClassRequiresCoversFixerTest extends AbstractFixerTestCase
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
        return [
            'already with annotation: @covers' => [
                '<?php
                    /**
                     * @covers Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'already with annotation: @coversDefaultClass' => [
                '<?php
                    /**
                     * @coversDefaultClass
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'without docblock #1' => [
                '<?php

                    /**
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php

                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'without docblock #2 (class is final)' => [
                '<?php

                    /**
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php

                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'without docblock #2 (class is abstract)' => [
                '<?php
                    abstract class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'with docblock but annotation is missing' => [
                '<?php

                    /**
                     * Description.
                     *
                     * @since v2.2
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php

                    /**
                     * Description.
                     *
                     * @since v2.2
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'with one-line docblock but annotation is missing' => [
                '<?php

                    /** Description. */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'with 2-lines docblock but annotation is missing #1' => [
                '<?php

                    /** Description.
                     * @coversNothing
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php

                    /** Description.
                     */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'with 2-lines docblock but annotation is missing #2' => [
                '<?php

                    /**
                     * @coversNothing
                     * Description. */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php

                    /**
                     * Description. */
                    final class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'with comment instead of docblock' => [
                '<?php
                    /*
                     * @covers Foo
                     */
                    /**
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
                '<?php
                    /*
                     * @covers Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}
                ',
            ],
            'not a test class' => [
                '<?php

                    class Foo {}
                ',
            ],
            'multiple classes in one file' => [
                '<?php /** */

                    use \PHPUnit\Framework\TestCase;

                    /**
                     * Foo
                     * @coversNothing
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}

                    class Bar {}

                    /**
                     * @coversNothing
                     */
                    class Baz1 extends PHPUnit_Framework_TestCase {}

                    /**
                     * @coversNothing
                     */
                    class Baz2 extends \PHPUnit_Framework_TestCase {}

                    /**
                     * @coversNothing
                     */
                    class Baz3 extends \PHPUnit\Framework\TestCase {}

                    /**
                     * @coversNothing
                     */
                    class Baz4 extends TestCase {}
                ',
                '<?php /** */

                    use \PHPUnit\Framework\TestCase;

                    /**
                     * Foo
                     */
                    class FooTest extends \PHPUnit_Framework_TestCase {}

                    class Bar {}

                    class Baz1 extends PHPUnit_Framework_TestCase {}

                    class Baz2 extends \PHPUnit_Framework_TestCase {}

                    class Baz3 extends \PHPUnit\Framework\TestCase {}

                    class Baz4 extends TestCase {}
                ',
            ],
        ];
    }
}
