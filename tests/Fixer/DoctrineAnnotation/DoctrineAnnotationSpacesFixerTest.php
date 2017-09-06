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
     * @dataProvider provideFixAllCases
     */
    public function testFixAll($expected, $input = null)
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
     * @dataProvider provideFixAllCases
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
    public function provideFixAllCases()
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
     * @dataProvider provideFixAroundParenthesesOnlyCases
     */
    public function testFixAroundParenthesesOnly($expected, $input = null)
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
     * @dataProvider provideFixAroundParenthesesOnlyCases
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
    public function provideFixAroundParenthesesOnlyCases()
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
     * @dataProvider provideFixAroundCommasOnlyCases
     */
    public function testFixAroundCommasOnly($expected, $input = null)
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
     * @dataProvider provideFixAroundCommasOnlyCases
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
    public function provideFixAroundCommasOnlyCases()
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
     * @dataProvider provideFixAroundArgumentAssignmentsOnlyCases
     */
    public function testFixAroundArgumentAssignmentsOnly($expected, $input = null)
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
     * @dataProvider provideFixAroundArgumentAssignmentsOnlyCases
     */
    public function testFixAroundArgumentAssignmentsOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAroundArgumentAssignmentsOnly($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixAroundArgumentAssignmentsOnlyCases()
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
     * @dataProvider provideFixAroundArrayAssignmentsOnlyCases
     */
    public function testFixAroundArrayAssignmentsOnly($expected, $input = null)
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
     * @dataProvider provideFixAroundArrayAssignmentsOnlyCases
     */
    public function testFixAroundArrayAssignmentsOnlyWithDifferentLineEnding($expected, $input = null)
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAroundArrayAssignmentsOnly($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixAroundArrayAssignmentsOnlyCases()
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
     * @return array
     */
    public function getInvalidConfigurationCases()
    {
        return array_merge(parent::getInvalidConfigurationCases(), [
            [[
                'around_parentheses' => false,
                'around_commas' => false,
                'around_argument_assignments' => false,
                'around_array_assignments' => false,
            ]],
        ]);
    }
}
