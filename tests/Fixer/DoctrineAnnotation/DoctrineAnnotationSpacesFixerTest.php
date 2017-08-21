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

namespace PhpCsFixer\Tests\Fixer\DoctrineAnnotation;

use PhpCsFixer\Tests\AbstractDoctrineAnnotationFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\AbstractDoctrineAnnotationFixer
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer
 */
final class DoctrineAnnotationSpacesFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAllCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAll($expected, $input = null)
    {
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => true,
            'around_commas' => true,
            'around_argument_assignments' => true,
            'around_array_assignments' => true,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAllCases
     */
    public function testFixAll($expected, $input = null)
    {
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => true,
            'around_commas' => true,
            'before_argument_assignments' => false,
            'after_argument_assignments' => false,
            'before_array_assignments_equals' => true,
            'after_array_assignments_equals' => true,
            'before_array_assignments_colon' => true,
            'after_array_assignments_colon' => true,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAllCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAllWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testLegacyFixAll($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAllCases
     */
    public function testFixAllWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAll($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAllCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo
 */'],
            ['
/**
 * @Foo()
 */'],
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo(foo="foo", bar="bar")
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo ( foo = "foo" ,bar = "bar" )
 */'],
            ['
/**
 * @Foo(
 *     foo="foo",
 *     bar="bar"
 * )
 */', '
/**
 * @Foo (
 *     foo = "foo" ,
 *     bar = "bar"
 * )
 */'],
            ['
/**
 * @Foo(
 *     @Bar("foo", "bar"),
 *     @Baz
 * )
 */', '
/**
 * @Foo(
 *     @Bar ( "foo" ,"bar") ,
 *     @Baz
 * )
 */'],
            ['
/**
 * @Foo({"bar", "baz"})
 */', '
/**
 * @Foo( {"bar" ,"baz"} )
 */'],
            ['
/**
 * @Foo(foo="=foo", bar={"foo" : "=foo", "bar" = "=bar"})
 */', '
/**
 * @Foo(foo = "=foo" ,bar = {"foo" : "=foo", "bar"="=bar"})
 */'],
            [
                '/** @Foo(foo="foo", bar={"foo" : "foo", "bar" = "bar"}) */',
                '/** @Foo ( foo = "foo" ,bar = {"foo" : "foo", "bar"="bar"} ) */',
            ],
            ['
/**
 * @Foo(
 *     foo="foo",
 *     bar={
 *         "foo" : "foo",
 *         "bar" = "bar"
 *     }
 * )
 */', '
/**
 * @Foo(
 *     foo = "foo"
 *     ,
 *     bar = {
 *         "foo":"foo",
 *         "bar"="bar"
 *     }
 * )
 */'],
            ['
/**
 * @Foo(
 *     foo="foo",
 *     bar={
 *         "foo" : "foo",
 *         "bar" = "bar"
 *     }
 * )
 */', '
/**
 * @Foo
 * (
 *     foo
 *     =
 *     "foo",
 *     bar
 *     =
 *     {
 *         "foo"
 *         :
 *         "foo",
 *         "bar"
 *         =
 *         "bar"
 *     }
 * )
 */'],
            ['
/**
 * @Foo(foo="foo", "bar"=@Bar\Baz({"foo" : true, "bar" = false}))
 */', '
/**
 * @Foo   (   foo = "foo", "bar" = @Bar\Baz({"foo":true, "bar"=false})   )
 */'],
            ['
/**
 * @Foo(foo = "foo" ,bar="bar"
 */'],
            ['
/**
 * Comment , with a comma.
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo(foo="string "" with inner quote", bar="string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */', '
/**
 * Description with a single " character.
 *
 * @Foo( foo="string "" with inner quote" ,bar="string "" with inner quote" )
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract ( foo,bar  =  "baz" )
 * @access ( foo,bar  =  "baz" )
 * @code ( foo,bar  =  "baz" )
 * @deprec ( foo,bar  =  "baz" )
 * @encode ( foo,bar  =  "baz" )
 * @exception ( foo,bar  =  "baz" )
 * @final ( foo,bar  =  "baz" )
 * @ingroup ( foo,bar  =  "baz" )
 * @inheritdoc ( foo,bar  =  "baz" )
 * @inheritDoc ( foo,bar  =  "baz" )
 * @magic ( foo,bar  =  "baz" )
 * @name ( foo,bar  =  "baz" )
 * @toc ( foo,bar  =  "baz" )
 * @tutorial ( foo,bar  =  "baz" )
 * @private ( foo,bar  =  "baz" )
 * @static ( foo,bar  =  "baz" )
 * @staticvar ( foo,bar  =  "baz" )
 * @staticVar ( foo,bar  =  "baz" )
 * @throw ( foo,bar  =  "baz" )
 *
 * // PHPDocumentor 2
 * @api ( foo,bar  =  "baz" )
 * @author ( foo,bar  =  "baz" )
 * @category ( foo,bar  =  "baz" )
 * @copyright ( foo,bar  =  "baz" )
 * @deprecated ( foo,bar  =  "baz" )
 * @example ( foo,bar  =  "baz" )
 * @filesource ( foo,bar  =  "baz" )
 * @global ( foo,bar  =  "baz" )
 * @ignore ( foo,bar  =  "baz" )
 * @internal ( foo,bar  =  "baz" )
 * @license ( foo,bar  =  "baz" )
 * @link ( foo,bar  =  "baz" )
 * @method ( foo,bar  =  "baz" )
 * @package ( foo,bar  =  "baz" )
 * @param ( foo,bar  =  "baz" )
 * @property ( foo,bar  =  "baz" )
 * @property-read ( foo,bar  =  "baz" )
 * @property-write ( foo,bar  =  "baz" )
 * @return ( foo,bar  =  "baz" )
 * @see ( foo,bar  =  "baz" )
 * @since ( foo,bar  =  "baz" )
 * @source ( foo,bar  =  "baz" )
 * @subpackage ( foo,bar  =  "baz" )
 * @throws ( foo,bar  =  "baz" )
 * @todo ( foo,bar  =  "baz" )
 * @TODO ( foo,bar  =  "baz" )
 * @usedBy ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 * @var ( foo,bar  =  "baz" )
 * @version ( foo,bar  =  "baz" )
 *
 * // PHPUnit
 * @after ( foo,bar  =  "baz" )
 * @afterClass ( foo,bar  =  "baz" )
 * @backupGlobals ( foo,bar  =  "baz" )
 * @backupStaticAttributes ( foo,bar  =  "baz" )
 * @before ( foo,bar  =  "baz" )
 * @beforeClass ( foo,bar  =  "baz" )
 * @codeCoverageIgnore ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreStart ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreEnd ( foo,bar  =  "baz" )
 * @covers ( foo,bar  =  "baz" )
 * @coversDefaultClass ( foo,bar  =  "baz" )
 * @coversNothing ( foo,bar  =  "baz" )
 * @dataProvider ( foo,bar  =  "baz" )
 * @depends ( foo,bar  =  "baz" )
 * @expectedException ( foo,bar  =  "baz" )
 * @expectedExceptionCode ( foo,bar  =  "baz" )
 * @expectedExceptionMessage ( foo,bar  =  "baz" )
 * @expectedExceptionMessageRegExp ( foo,bar  =  "baz" )
 * @group ( foo,bar  =  "baz" )
 * @large ( foo,bar  =  "baz" )
 * @medium ( foo,bar  =  "baz" )
 * @preserveGlobalState ( foo,bar  =  "baz" )
 * @requires ( foo,bar  =  "baz" )
 * @runTestsInSeparateProcesses ( foo,bar  =  "baz" )
 * @runInSeparateProcess ( foo,bar  =  "baz" )
 * @small ( foo,bar  =  "baz" )
 * @test ( foo,bar  =  "baz" )
 * @testdox ( foo,bar  =  "baz" )
 * @ticket ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 *
 * // PHPCheckStyle
 * @SuppressWarnings ( foo,bar  =  "baz" )
 *
 * // PHPStorm
 * @noinspection ( foo,bar  =  "baz" )
 *
 * // PEAR
 * @package_version ( foo,bar  =  "baz" )
 *
 * // PlantUML
 * @enduml ( foo,bar  =  "baz" )
 * @startuml ( foo,bar  =  "baz" )
 *
 * // other
 * @fix ( foo,bar  =  "baz" )
 * @FIXME ( foo,bar  =  "baz" )
 * @fixme ( foo,bar  =  "baz" )
 * @override
 */'],
            ['
/**
 * @Transform /^(\d+)$/

 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundParenthesesOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundParenthesesOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_commas' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ]);
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => true,
            'around_commas' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundParenthesesOnlyCases
     */
    public function testFixAroundParenthesesOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => true,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundParenthesesOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundParenthesesOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testLegacyFixAroundParenthesesOnly($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundParenthesesOnlyCases
     */
    public function testFixAroundParenthesesOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAroundParenthesesOnly($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundParenthesesOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo
 */'],
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo()
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo ( )
 */'],
            ['
/**
 * @Foo("bar")
 */', '
/**
 * @Foo( "bar" )
 */'],
            ['
/**
 * @Foo("bar", "baz")
 */', '
/**
 * @Foo( "bar", "baz" )
 */'],
            [
                '/** @Foo("bar", "baz") */',
                '/** @Foo( "bar", "baz" ) */',
            ],
            ['
/**
 * @Foo("bar", "baz")
 */', '
/**
 * @Foo(     "bar", "baz"     )
 */'],
            ['
/**
 * @Foo("bar", "baz")
 */', '
/**
 * @Foo    (     "bar", "baz"     )
 */'],
            ['
/**
 * @Foo(
 *     "bar",
 *     "baz"
 * )
 */', '
/**
 * @Foo
 * (
 *     "bar",
 *     "baz"
 * )
 */'],
            ['
/**
 * @Foo(
 *     @Bar("baz")
 * )
 */', '
/**
 * @Foo
 * (
 *     @Bar ( "baz" )
 * )
 */'],
            ['
/**
 * @Foo ( @Bar ( "bar" )
 */'],
            ['
/**
 * Foo ( Bar Baz )
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo("string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */', '
/**
 * Description with a single " character.
 *
 * @Foo ( "string "" with inner quote" )
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract ( foo,bar  =  "baz" )
 * @access ( foo,bar  =  "baz" )
 * @code ( foo,bar  =  "baz" )
 * @deprec ( foo,bar  =  "baz" )
 * @encode ( foo,bar  =  "baz" )
 * @exception ( foo,bar  =  "baz" )
 * @final ( foo,bar  =  "baz" )
 * @ingroup ( foo,bar  =  "baz" )
 * @inheritdoc ( foo,bar  =  "baz" )
 * @inheritDoc ( foo,bar  =  "baz" )
 * @magic ( foo,bar  =  "baz" )
 * @name ( foo,bar  =  "baz" )
 * @toc ( foo,bar  =  "baz" )
 * @tutorial ( foo,bar  =  "baz" )
 * @private ( foo,bar  =  "baz" )
 * @static ( foo,bar  =  "baz" )
 * @staticvar ( foo,bar  =  "baz" )
 * @staticVar ( foo,bar  =  "baz" )
 * @throw ( foo,bar  =  "baz" )
 *
 * // PHPDocumentor 2
 * @api ( foo,bar  =  "baz" )
 * @author ( foo,bar  =  "baz" )
 * @category ( foo,bar  =  "baz" )
 * @copyright ( foo,bar  =  "baz" )
 * @deprecated ( foo,bar  =  "baz" )
 * @example ( foo,bar  =  "baz" )
 * @filesource ( foo,bar  =  "baz" )
 * @global ( foo,bar  =  "baz" )
 * @ignore ( foo,bar  =  "baz" )
 * @internal ( foo,bar  =  "baz" )
 * @license ( foo,bar  =  "baz" )
 * @link ( foo,bar  =  "baz" )
 * @method ( foo,bar  =  "baz" )
 * @package ( foo,bar  =  "baz" )
 * @param ( foo,bar  =  "baz" )
 * @property ( foo,bar  =  "baz" )
 * @property-read ( foo,bar  =  "baz" )
 * @property-write ( foo,bar  =  "baz" )
 * @return ( foo,bar  =  "baz" )
 * @see ( foo,bar  =  "baz" )
 * @since ( foo,bar  =  "baz" )
 * @source ( foo,bar  =  "baz" )
 * @subpackage ( foo,bar  =  "baz" )
 * @throws ( foo,bar  =  "baz" )
 * @todo ( foo,bar  =  "baz" )
 * @TODO ( foo,bar  =  "baz" )
 * @usedBy ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 * @var ( foo,bar  =  "baz" )
 * @version ( foo,bar  =  "baz" )
 *
 * // PHPUnit
 * @after ( foo,bar  =  "baz" )
 * @afterClass ( foo,bar  =  "baz" )
 * @backupGlobals ( foo,bar  =  "baz" )
 * @backupStaticAttributes ( foo,bar  =  "baz" )
 * @before ( foo,bar  =  "baz" )
 * @beforeClass ( foo,bar  =  "baz" )
 * @codeCoverageIgnore ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreStart ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreEnd ( foo,bar  =  "baz" )
 * @covers ( foo,bar  =  "baz" )
 * @coversDefaultClass ( foo,bar  =  "baz" )
 * @coversNothing ( foo,bar  =  "baz" )
 * @dataProvider ( foo,bar  =  "baz" )
 * @depends ( foo,bar  =  "baz" )
 * @expectedException ( foo,bar  =  "baz" )
 * @expectedExceptionCode ( foo,bar  =  "baz" )
 * @expectedExceptionMessage ( foo,bar  =  "baz" )
 * @expectedExceptionMessageRegExp ( foo,bar  =  "baz" )
 * @group ( foo,bar  =  "baz" )
 * @large ( foo,bar  =  "baz" )
 * @medium ( foo,bar  =  "baz" )
 * @preserveGlobalState ( foo,bar  =  "baz" )
 * @requires ( foo,bar  =  "baz" )
 * @runTestsInSeparateProcesses ( foo,bar  =  "baz" )
 * @runInSeparateProcess ( foo,bar  =  "baz" )
 * @small ( foo,bar  =  "baz" )
 * @test ( foo,bar  =  "baz" )
 * @testdox ( foo,bar  =  "baz" )
 * @ticket ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 *
 * // PHPCheckStyle
 * @SuppressWarnings ( foo,bar  =  "baz" )
 *
 * // PHPStorm
 * @noinspection ( foo,bar  =  "baz" )
 *
 * // PEAR
 * @package_version ( foo,bar  =  "baz" )
 *
 * // PlantUML
 * @enduml ( foo,bar  =  "baz" )
 * @startuml ( foo,bar  =  "baz" )
 *
 * // other
 * @fix ( foo,bar  =  "baz" )
 * @FIXME ( foo,bar  =  "baz" )
 * @fixme ( foo,bar  =  "baz" )
 * @override
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundCommasOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundCommasOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ]);
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => true,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundCommasOnlyCases
     */
    public function testFixAroundCommasOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => true,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundCommasOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundCommasOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testLegacyFixAroundCommasOnly($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundCommasOnlyCases
     */
    public function testFixAroundCommasOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAroundCommasOnly($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundCommasOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo
 */'],
            ['
/**
 * @Foo()
 */'],
            ['
/**
 * @Foo ()
 */'],
            ['
/**
 * @Foo( "bar" )
 */'],
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo( "bar", "baz")
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo( "bar" ,"baz")
 */'],
            [
                '/** @Foo( "bar", "baz") */',
                '/** @Foo( "bar" ,"baz") */',
            ],
            ['
/**
 * @Foo( "bar", "baz")
 */', '
/**
 * @Foo( "bar" , "baz")
 */'],
            ['
/**
 * @Foo(
 *     "bar",
 *     "baz"
 * )
 */', '
/**
 * @Foo(
 *     "bar" ,
 *     "baz"
 * )
 */'],
            ['
/**
 * @Foo(
 *     "bar",
 *     "baz"
 * )
 */', '
/**
 * @Foo(
 *     "bar"
 *     ,
 *     "baz"
 * )
 */'],
            ['
/**
 * @Foo("bar ,", "baz,")
 */'],
            ['
/**
 * @Foo(
 *     @Bar ( "foo", "bar"),
 *     @Baz
 * )
 */', '
/**
 * @Foo(
 *     @Bar ( "foo" ,"bar") ,
 *     @Baz
 * )
 */'],
            ['
/**
 * @Foo({"bar", "baz"})
 */', '
/**
 * @Foo({"bar" ,"baz"})
 */'],
            ['
/**
 * @Foo(foo="foo", bar="bar")
 */', '
/**
 * @Foo(foo="foo" ,bar="bar")
 */'],
            ['
/**
 * @Foo(foo="foo" ,bar="bar"
 */'],
            ['
/**
 * Comment , with a comma.
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo(foo="string "" with inner quote", bar="string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */', '
/**
 * Description with a single " character.
 *
 * @Foo(foo="string "" with inner quote" ,bar="string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract ( foo,bar  =  "baz" )
 * @access ( foo,bar  =  "baz" )
 * @code ( foo,bar  =  "baz" )
 * @deprec ( foo,bar  =  "baz" )
 * @encode ( foo,bar  =  "baz" )
 * @exception ( foo,bar  =  "baz" )
 * @final ( foo,bar  =  "baz" )
 * @ingroup ( foo,bar  =  "baz" )
 * @inheritdoc ( foo,bar  =  "baz" )
 * @inheritDoc ( foo,bar  =  "baz" )
 * @magic ( foo,bar  =  "baz" )
 * @name ( foo,bar  =  "baz" )
 * @toc ( foo,bar  =  "baz" )
 * @tutorial ( foo,bar  =  "baz" )
 * @private ( foo,bar  =  "baz" )
 * @static ( foo,bar  =  "baz" )
 * @staticvar ( foo,bar  =  "baz" )
 * @staticVar ( foo,bar  =  "baz" )
 * @throw ( foo,bar  =  "baz" )
 *
 * // PHPDocumentor 2
 * @api ( foo,bar  =  "baz" )
 * @author ( foo,bar  =  "baz" )
 * @category ( foo,bar  =  "baz" )
 * @copyright ( foo,bar  =  "baz" )
 * @deprecated ( foo,bar  =  "baz" )
 * @example ( foo,bar  =  "baz" )
 * @filesource ( foo,bar  =  "baz" )
 * @global ( foo,bar  =  "baz" )
 * @ignore ( foo,bar  =  "baz" )
 * @internal ( foo,bar  =  "baz" )
 * @license ( foo,bar  =  "baz" )
 * @link ( foo,bar  =  "baz" )
 * @method ( foo,bar  =  "baz" )
 * @package ( foo,bar  =  "baz" )
 * @param ( foo,bar  =  "baz" )
 * @property ( foo,bar  =  "baz" )
 * @property-read ( foo,bar  =  "baz" )
 * @property-write ( foo,bar  =  "baz" )
 * @return ( foo,bar  =  "baz" )
 * @see ( foo,bar  =  "baz" )
 * @since ( foo,bar  =  "baz" )
 * @source ( foo,bar  =  "baz" )
 * @subpackage ( foo,bar  =  "baz" )
 * @throws ( foo,bar  =  "baz" )
 * @todo ( foo,bar  =  "baz" )
 * @TODO ( foo,bar  =  "baz" )
 * @usedBy ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 * @var ( foo,bar  =  "baz" )
 * @version ( foo,bar  =  "baz" )
 *
 * // PHPUnit
 * @after ( foo,bar  =  "baz" )
 * @afterClass ( foo,bar  =  "baz" )
 * @backupGlobals ( foo,bar  =  "baz" )
 * @backupStaticAttributes ( foo,bar  =  "baz" )
 * @before ( foo,bar  =  "baz" )
 * @beforeClass ( foo,bar  =  "baz" )
 * @codeCoverageIgnore ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreStart ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreEnd ( foo,bar  =  "baz" )
 * @covers ( foo,bar  =  "baz" )
 * @coversDefaultClass ( foo,bar  =  "baz" )
 * @coversNothing ( foo,bar  =  "baz" )
 * @dataProvider ( foo,bar  =  "baz" )
 * @depends ( foo,bar  =  "baz" )
 * @expectedException ( foo,bar  =  "baz" )
 * @expectedExceptionCode ( foo,bar  =  "baz" )
 * @expectedExceptionMessage ( foo,bar  =  "baz" )
 * @expectedExceptionMessageRegExp ( foo,bar  =  "baz" )
 * @group ( foo,bar  =  "baz" )
 * @large ( foo,bar  =  "baz" )
 * @medium ( foo,bar  =  "baz" )
 * @preserveGlobalState ( foo,bar  =  "baz" )
 * @requires ( foo,bar  =  "baz" )
 * @runTestsInSeparateProcesses ( foo,bar  =  "baz" )
 * @runInSeparateProcess ( foo,bar  =  "baz" )
 * @small ( foo,bar  =  "baz" )
 * @test ( foo,bar  =  "baz" )
 * @testdox ( foo,bar  =  "baz" )
 * @ticket ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 *
 * // PHPCheckStyle
 * @SuppressWarnings ( foo,bar  =  "baz" )
 *
 * // PHPStorm
 * @noinspection ( foo,bar  =  "baz" )
 *
 * // PEAR
 * @package_version ( foo,bar  =  "baz" )
 *
 * // PlantUML
 * @enduml ( foo,bar  =  "baz" )
 * @startuml ( foo,bar  =  "baz" )
 *
 * // other
 * @fix ( foo,bar  =  "baz" )
 * @FIXME ( foo,bar  =  "baz" )
 * @fixme ( foo,bar  =  "baz" )
 * @override
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundArgumentAssignmentsOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundArgumentAssignmentsOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'around_array_assignments' => false,
        ]);
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'around_argument_assignments' => true,
            'around_array_assignments' => false,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundArgumentAssignmentsOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundArgumentAssignmentsOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testLegacyFixAroundArgumentAssignmentsOnly($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundArgumentAssignmentsOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo (foo="foo", bar="bar")
 */'],
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo(foo="foo", bar="bar")
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo(foo = "foo", bar = "bar")
 */'],
            ['
/**
 * @Foo(
 *     foo="foo",
 *     bar="bar"
 * )
 */', '
/**
 * @Foo(
 *     foo = "foo",
 *     bar = "bar"
 * )
 */'],
            ['
/**
 * @Foo(foo="foo", bar={"foo" : "foo", "bar"="bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"})
 */'],
            [
                '/** @Foo(foo="foo", bar={"foo" : "foo", "bar"="bar"}) */',
                '/** @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"}) */',
            ],
            ['
/**
 * @Foo(
 *     foo="foo",
 *     bar={
 *         "foo":"foo",
 *         "bar"="bar"
 *     }
 * )
 */', '
/**
 * @Foo(
 *     foo = "foo",
 *     bar = {
 *         "foo":"foo",
 *         "bar"="bar"
 *     }
 * )
 */'],
            ['
/**
 * @Foo(
 *     foo="foo",
 *     bar={
 *         "foo"
 *         :
 *         "foo",
 *         "bar"
 *         =
 *         "bar"
 *     }
 * )
 */', '
/**
 * @Foo(
 *     foo
 *     =
 *     "foo",
 *     bar
 *     =
 *     {
 *         "foo"
 *         :
 *         "foo",
 *         "bar"
 *         =
 *         "bar"
 *     }
 * )
 */'],
            ['
/**
 * @Foo(foo="foo", "bar"=@Bar\Baz({"foo":true, "bar"=false}))
 */', '
/**
 * @Foo(foo = "foo", "bar" = @Bar\Baz({"foo":true, "bar"=false}))
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo(foo="string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */', '
/**
 * Description with a single " character.
 *
 * @Foo(foo = "string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract ( foo,bar  =  "baz" )
 * @access ( foo,bar  =  "baz" )
 * @code ( foo,bar  =  "baz" )
 * @deprec ( foo,bar  =  "baz" )
 * @encode ( foo,bar  =  "baz" )
 * @exception ( foo,bar  =  "baz" )
 * @final ( foo,bar  =  "baz" )
 * @ingroup ( foo,bar  =  "baz" )
 * @inheritdoc ( foo,bar  =  "baz" )
 * @inheritDoc ( foo,bar  =  "baz" )
 * @magic ( foo,bar  =  "baz" )
 * @name ( foo,bar  =  "baz" )
 * @toc ( foo,bar  =  "baz" )
 * @tutorial ( foo,bar  =  "baz" )
 * @private ( foo,bar  =  "baz" )
 * @static ( foo,bar  =  "baz" )
 * @staticvar ( foo,bar  =  "baz" )
 * @staticVar ( foo,bar  =  "baz" )
 * @throw ( foo,bar  =  "baz" )
 *
 * // PHPDocumentor 2
 * @api ( foo,bar  =  "baz" )
 * @author ( foo,bar  =  "baz" )
 * @category ( foo,bar  =  "baz" )
 * @copyright ( foo,bar  =  "baz" )
 * @deprecated ( foo,bar  =  "baz" )
 * @example ( foo,bar  =  "baz" )
 * @filesource ( foo,bar  =  "baz" )
 * @global ( foo,bar  =  "baz" )
 * @ignore ( foo,bar  =  "baz" )
 * @internal ( foo,bar  =  "baz" )
 * @license ( foo,bar  =  "baz" )
 * @link ( foo,bar  =  "baz" )
 * @method ( foo,bar  =  "baz" )
 * @package ( foo,bar  =  "baz" )
 * @param ( foo,bar  =  "baz" )
 * @property ( foo,bar  =  "baz" )
 * @property-read ( foo,bar  =  "baz" )
 * @property-write ( foo,bar  =  "baz" )
 * @return ( foo,bar  =  "baz" )
 * @see ( foo,bar  =  "baz" )
 * @since ( foo,bar  =  "baz" )
 * @source ( foo,bar  =  "baz" )
 * @subpackage ( foo,bar  =  "baz" )
 * @throws ( foo,bar  =  "baz" )
 * @todo ( foo,bar  =  "baz" )
 * @TODO ( foo,bar  =  "baz" )
 * @usedBy ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 * @var ( foo,bar  =  "baz" )
 * @version ( foo,bar  =  "baz" )
 *
 * // PHPUnit
 * @after ( foo,bar  =  "baz" )
 * @afterClass ( foo,bar  =  "baz" )
 * @backupGlobals ( foo,bar  =  "baz" )
 * @backupStaticAttributes ( foo,bar  =  "baz" )
 * @before ( foo,bar  =  "baz" )
 * @beforeClass ( foo,bar  =  "baz" )
 * @codeCoverageIgnore ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreStart ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreEnd ( foo,bar  =  "baz" )
 * @covers ( foo,bar  =  "baz" )
 * @coversDefaultClass ( foo,bar  =  "baz" )
 * @coversNothing ( foo,bar  =  "baz" )
 * @dataProvider ( foo,bar  =  "baz" )
 * @depends ( foo,bar  =  "baz" )
 * @expectedException ( foo,bar  =  "baz" )
 * @expectedExceptionCode ( foo,bar  =  "baz" )
 * @expectedExceptionMessage ( foo,bar  =  "baz" )
 * @expectedExceptionMessageRegExp ( foo,bar  =  "baz" )
 * @group ( foo,bar  =  "baz" )
 * @large ( foo,bar  =  "baz" )
 * @medium ( foo,bar  =  "baz" )
 * @preserveGlobalState ( foo,bar  =  "baz" )
 * @requires ( foo,bar  =  "baz" )
 * @runTestsInSeparateProcesses ( foo,bar  =  "baz" )
 * @runInSeparateProcess ( foo,bar  =  "baz" )
 * @small ( foo,bar  =  "baz" )
 * @test ( foo,bar  =  "baz" )
 * @testdox ( foo,bar  =  "baz" )
 * @ticket ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 *
 * // PHPCheckStyle
 * @SuppressWarnings ( foo,bar  =  "baz" )
 *
 * // PHPStorm
 * @noinspection ( foo,bar  =  "baz" )
 *
 * // PEAR
 * @package_version ( foo,bar  =  "baz" )
 *
 * // PlantUML
 * @enduml ( foo,bar  =  "baz" )
 * @startuml ( foo,bar  =  "baz" )
 *
 * // other
 * @fix ( foo,bar  =  "baz" )
 * @FIXME ( foo,bar  =  "baz" )
 * @fixme ( foo,bar  =  "baz" )
 * @override
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundArrayAssignmentsOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundArrayAssignmentsOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'around_argument_assignments' => false,
        ]);
        $this->doTest($expected, $input);

        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => true,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixAroundArrayAssignmentsOnlyCases
     * @group legacy
     * @expectedDeprecation Option "around_argument_assignments" is deprecated and will be removed in 3.0, use options "before_argument_assignments" and "after_argument_assignments" instead.
     * @expectedDeprecation Option "around_array_assignments" is deprecated and will be removed in 3.0, use options "before_array_assignments_equals", "after_array_assignments_equals", "before_array_assignments_colon" and "after_array_assignments_colon" instead.
     */
    public function testLegacyFixAroundArrayAssignmentsOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testLegacyFixAroundArrayAssignmentsOnly($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundArrayAssignmentsOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo (foo="foo", bar="bar")
 */'],
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo(foo = "foo", bar = "bar")
 */'],
            ['
/**
 * @Foo(
 *     foo = "foo",
 *     bar = "bar"
 * )
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"})
 */'],
            [
                '/** @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"}) */',
                '/** @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"}) */',
            ],
            ['
/**
 * @Foo(
 *     foo = "foo",
 *     bar = {
 *         "foo" : "foo",
 *         "bar" = "bar"
 *     }
 * )
 */', '
/**
 * @Foo(
 *     foo = "foo",
 *     bar = {
 *         "foo":"foo",
 *         "bar"="bar"
 *     }
 * )
 */'],
            ['
/**
 * @Foo(
 *     foo
 *     =
 *     "foo",
 *     bar
 *     =
 *     {
 *         "foo" : "foo",
 *         "bar" = "bar"
 *     }
 * )
 */', '
/**
 * @Foo(
 *     foo
 *     =
 *     "foo",
 *     bar
 *     =
 *     {
 *         "foo"
 *         :
 *         "foo",
 *         "bar"
 *         =
 *         "bar"
 *     }
 * )
 */'],
            ['
/**
 * @Foo(foo = "foo", "bar" = @Bar\Baz({"foo" : true, "bar" = false}))
 */', '
/**
 * @Foo(foo = "foo", "bar" = @Bar\Baz({"foo":true, "bar"=false}))
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo(foo = "string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract ( foo,bar  =  "baz" )
 * @access ( foo,bar  =  "baz" )
 * @code ( foo,bar  =  "baz" )
 * @deprec ( foo,bar  =  "baz" )
 * @encode ( foo,bar  =  "baz" )
 * @exception ( foo,bar  =  "baz" )
 * @final ( foo,bar  =  "baz" )
 * @ingroup ( foo,bar  =  "baz" )
 * @inheritdoc ( foo,bar  =  "baz" )
 * @inheritDoc ( foo,bar  =  "baz" )
 * @magic ( foo,bar  =  "baz" )
 * @name ( foo,bar  =  "baz" )
 * @toc ( foo,bar  =  "baz" )
 * @tutorial ( foo,bar  =  "baz" )
 * @private ( foo,bar  =  "baz" )
 * @static ( foo,bar  =  "baz" )
 * @staticvar ( foo,bar  =  "baz" )
 * @staticVar ( foo,bar  =  "baz" )
 * @throw ( foo,bar  =  "baz" )
 *
 * // PHPDocumentor 2
 * @api ( foo,bar  =  "baz" )
 * @author ( foo,bar  =  "baz" )
 * @category ( foo,bar  =  "baz" )
 * @copyright ( foo,bar  =  "baz" )
 * @deprecated ( foo,bar  =  "baz" )
 * @example ( foo,bar  =  "baz" )
 * @filesource ( foo,bar  =  "baz" )
 * @global ( foo,bar  =  "baz" )
 * @ignore ( foo,bar  =  "baz" )
 * @internal ( foo,bar  =  "baz" )
 * @license ( foo,bar  =  "baz" )
 * @link ( foo,bar  =  "baz" )
 * @method ( foo,bar  =  "baz" )
 * @package ( foo,bar  =  "baz" )
 * @param ( foo,bar  =  "baz" )
 * @property ( foo,bar  =  "baz" )
 * @property-read ( foo,bar  =  "baz" )
 * @property-write ( foo,bar  =  "baz" )
 * @return ( foo,bar  =  "baz" )
 * @see ( foo,bar  =  "baz" )
 * @since ( foo,bar  =  "baz" )
 * @source ( foo,bar  =  "baz" )
 * @subpackage ( foo,bar  =  "baz" )
 * @throws ( foo,bar  =  "baz" )
 * @todo ( foo,bar  =  "baz" )
 * @TODO ( foo,bar  =  "baz" )
 * @usedBy ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 * @var ( foo,bar  =  "baz" )
 * @version ( foo,bar  =  "baz" )
 *
 * // PHPUnit
 * @after ( foo,bar  =  "baz" )
 * @afterClass ( foo,bar  =  "baz" )
 * @backupGlobals ( foo,bar  =  "baz" )
 * @backupStaticAttributes ( foo,bar  =  "baz" )
 * @before ( foo,bar  =  "baz" )
 * @beforeClass ( foo,bar  =  "baz" )
 * @codeCoverageIgnore ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreStart ( foo,bar  =  "baz" )
 * @codeCoverageIgnoreEnd ( foo,bar  =  "baz" )
 * @covers ( foo,bar  =  "baz" )
 * @coversDefaultClass ( foo,bar  =  "baz" )
 * @coversNothing ( foo,bar  =  "baz" )
 * @dataProvider ( foo,bar  =  "baz" )
 * @depends ( foo,bar  =  "baz" )
 * @expectedException ( foo,bar  =  "baz" )
 * @expectedExceptionCode ( foo,bar  =  "baz" )
 * @expectedExceptionMessage ( foo,bar  =  "baz" )
 * @expectedExceptionMessageRegExp ( foo,bar  =  "baz" )
 * @group ( foo,bar  =  "baz" )
 * @large ( foo,bar  =  "baz" )
 * @medium ( foo,bar  =  "baz" )
 * @preserveGlobalState ( foo,bar  =  "baz" )
 * @requires ( foo,bar  =  "baz" )
 * @runTestsInSeparateProcesses ( foo,bar  =  "baz" )
 * @runInSeparateProcess ( foo,bar  =  "baz" )
 * @small ( foo,bar  =  "baz" )
 * @test ( foo,bar  =  "baz" )
 * @testdox ( foo,bar  =  "baz" )
 * @ticket ( foo,bar  =  "baz" )
 * @uses ( foo,bar  =  "baz" )
 *
 * // PHPCheckStyle
 * @SuppressWarnings ( foo,bar  =  "baz" )
 *
 * // PHPStorm
 * @noinspection ( foo,bar  =  "baz" )
 *
 * // PEAR
 * @package_version ( foo,bar  =  "baz" )
 *
 * // PlantUML
 * @enduml ( foo,bar  =  "baz" )
 * @startuml ( foo,bar  =  "baz" )
 *
 * // other
 * @fix ( foo,bar  =  "baz" )
 * @FIXME ( foo,bar  =  "baz" )
 * @fixme ( foo,bar  =  "baz" )
 * @override
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceBeforeArgumentAssignmentOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => true,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceBeforeArgumentAssignmentOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceBeforeArgumentAssignmentOnly($expected, $input);
    }

    public function getFixWithSpaceBeforeArgumentAssignmentOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo ="foo", bar ={"foo":"foo", "bar"="bar"})
 */', '
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceBeforeArgumentAssignmentOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => false,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceBeforeArgumentAssignmentOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceBeforeArgumentAssignmentOnly($expected, $input);
    }

    public function getFixWithoutSpaceBeforeArgumentAssignmentOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo= "foo", bar= {"foo" : "foo", "bar" = "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceAfterArgumentAssignmentOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => true,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceAfterArgumentAssignmentOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceAfterArgumentAssignmentOnly($expected, $input);
    }

    public function getFixWithSpaceAfterArgumentAssignmentOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo= "foo", bar= {"foo":"foo", "bar"="bar"})
 */', '
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceAfterArgumentAssignmentOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => false,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceAfterArgumentAssignmentOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceAfterArgumentAssignmentOnly($expected, $input);
    }

    public function getFixWithoutSpaceAfterArgumentAssignmentOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo ="foo", bar ={"foo" : "foo", "bar" = "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentEqualOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => true,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentEqualOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceBeforeArrayAssignmentEqualOnly($expected, $input);
    }

    public function getFixWithSpaceBeforeArrayAssignmentEqualOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar" ="bar"})
 */', '
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentEqualOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => false,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentEqualOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceBeforeArrayAssignmentEqualOnly($expected, $input);
    }

    public function getFixWithoutSpaceBeforeArrayAssignmentEqualOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar"= "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentEqualOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => true,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentEqualOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceAfterArrayAssignmentEqualOnly($expected, $input);
    }

    public function getFixWithSpaceAfterArrayAssignmentEqualOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"= "bar"})
 */', '
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentEqualOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => false,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentEqualOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceAfterArrayAssignmentEqualOnly($expected, $input);
    }

    public function getFixWithoutSpaceAfterArrayAssignmentEqualOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" ="bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentColonOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => true,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentColonOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceBeforeArrayAssignmentColonOnly($expected, $input);
    }

    public function getFixWithSpaceBeforeArrayAssignmentColonOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo" :"foo", "bar"="bar"})
 */', '
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentColonOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => false,
            'after_array_assignments_colon' => null,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentColonOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceBeforeArrayAssignmentColonOnly($expected, $input);
    }

    public function getFixWithoutSpaceBeforeArrayAssignmentColonOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo": "foo", "bar" = "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentColonOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => true,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentColonOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceAfterArrayAssignmentColonOnly($expected, $input);
    }

    public function getFixWithSpaceAfterArrayAssignmentColonOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo": "foo", "bar"="bar"})
 */', '
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentColonOnly($expected, $input = null)
    {
        $this->fixer->configure([
            'around_parentheses' => false,
            'around_commas' => false,
            'before_argument_assignments' => null,
            'after_argument_assignments' => null,
            'before_array_assignments_equals' => null,
            'after_array_assignments_equals' => null,
            'before_array_assignments_colon' => null,
            'after_array_assignments_colon' => false,
        ]);
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider getFixWithoutSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentColonOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceAfterArrayAssignmentColonOnly($expected, $input);
    }

    public function getFixWithoutSpaceAfterArrayAssignmentColonOnlyCases()
    {
        return $this->createTestCases([
            ['
/**
 * @Foo(foo="foo", bar={"foo":"foo", "bar"="bar"})
 */'],
            ['
/**
 * @Foo(foo = "foo", bar = {"foo" :"foo", "bar" = "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */'],
        ]);
    }
}
