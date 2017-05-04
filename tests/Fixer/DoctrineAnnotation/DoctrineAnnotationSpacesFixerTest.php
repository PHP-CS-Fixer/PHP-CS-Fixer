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
     * @param string|null $input
     *
     * @dataProvider getFixAllCases
     */
    public function testFixAll($expected, $input = null)
    {
        $this->doTest($expected, $input);

        $this->fixer->configure(array(
            'around_parentheses' => true,
            'around_commas' => true,
            'around_argument_assignments' => true,
            'around_array_assignments' => true,
        ));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAllCases()
    {
        return $this->createTestCases(array(
            array('
/**
 * @Foo
 */'),
            array('
/**
 * @Foo()
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo({"bar", "baz"})
 */', '
/**
 * @Foo( {"bar" ,"baz"} )
 */'),
            array('
/**
 * @Foo(foo="=foo", bar={"foo" : "=foo", "bar" = "=bar"})
 */', '
/**
 * @Foo(foo = "=foo" ,bar = {"foo" : "=foo", "bar"="=bar"})
 */'),
            array(
                '/** @Foo(foo="foo", bar={"foo" : "foo", "bar" = "bar"}) */',
                '/** @Foo ( foo = "foo" ,bar = {"foo" : "foo", "bar"="bar"} ) */',
            ),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(foo="foo", "bar"=@Bar\Baz({"foo" : true, "bar" = false}))
 */', '
/**
 * @Foo   (   foo = "foo", "bar" = @Bar\Baz({"foo":true, "bar"=false})   )
 */'),
            array('
/**
 * @Foo(foo = "foo" ,bar="bar"
 */'),
            array('
/**
 * Comment , with a comma.
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Transform /^(\d+)$/
 */'),
        ));
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider getFixAroundParenthesesOnlyCases
     */
    public function testFixAroundParenthesesOnly($expected, $input = null)
    {
        $this->fixer->configure(array(
            'around_commas' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ));
        $this->doTest($expected, $input);

        $this->fixer->configure(array(
            'around_parentheses' => true,
            'around_commas' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundParenthesesOnlyCases()
    {
        return $this->createTestCases(array(
            array('
/**
 * @Foo
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo("bar")
 */', '
/**
 * @Foo( "bar" )
 */'),
            array('
/**
 * @Foo("bar", "baz")
 */', '
/**
 * @Foo( "bar", "baz" )
 */'),
            array(
                '/** @Foo("bar", "baz") */',
                '/** @Foo( "bar", "baz" ) */',
            ),
            array('
/**
 * @Foo("bar", "baz")
 */', '
/**
 * @Foo(     "bar", "baz"     )
 */'),
            array('
/**
 * @Foo("bar", "baz")
 */', '
/**
 * @Foo    (     "bar", "baz"     )
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo ( @Bar ( "bar" )
 */'),
            array('
/**
 * Foo ( Bar Baz )
 */'),
            array('
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
 */'),
            array('
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
 */'),
        ));
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider getFixAroundCommasOnlyCases
     */
    public function testFixAroundCommasOnly($expected, $input = null)
    {
        $this->fixer->configure(array(
            'around_parentheses' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ));
        $this->doTest($expected, $input);

        $this->fixer->configure(array(
            'around_parentheses' => false,
            'around_commas' => true,
            'around_argument_assignments' => false,
            'around_array_assignments' => false,
        ));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundCommasOnlyCases()
    {
        return $this->createTestCases(array(
            array('
/**
 * @Foo
 */'),
            array('
/**
 * @Foo()
 */'),
            array('
/**
 * @Foo ()
 */'),
            array('
/**
 * @Foo( "bar" )
 */'),
            array('
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
 */'),
            array(
                '/** @Foo( "bar", "baz") */',
                '/** @Foo( "bar" ,"baz") */',
            ),
            array('
/**
 * @Foo( "bar", "baz")
 */', '
/**
 * @Foo( "bar" , "baz")
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo("bar ,", "baz,")
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo({"bar", "baz"})
 */', '
/**
 * @Foo({"bar" ,"baz"})
 */'),
            array('
/**
 * @Foo(foo="foo", bar="bar")
 */', '
/**
 * @Foo(foo="foo" ,bar="bar")
 */'),
            array('
/**
 * @Foo(foo="foo" ,bar="bar"
 */'),
            array('
/**
 * Comment , with a comma.
 */'),
            array('
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
 */'),
            array('
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
 */'),
        ));
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider getFixAroundArgumentAssignmentsOnlyCases
     */
    public function testFixAroundArgumentAssignmentsOnly($expected, $input = null)
    {
        $this->fixer->configure(array(
            'around_parentheses' => false,
            'around_commas' => false,
            'around_array_assignments' => false,
        ));
        $this->doTest($expected, $input);

        $this->fixer->configure(array(
            'around_parentheses' => false,
            'around_commas' => false,
            'around_argument_assignments' => true,
            'around_array_assignments' => false,
        ));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundArgumentAssignmentsOnlyCases()
    {
        return $this->createTestCases(array(
            array('
/**
 * @Foo (foo="foo", bar="bar")
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(foo="foo", bar={"foo" : "foo", "bar"="bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"})
 */'),
            array(
                '/** @Foo(foo="foo", bar={"foo" : "foo", "bar"="bar"}) */',
                '/** @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"}) */',
            ),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(foo="foo", "bar"=@Bar\Baz({"foo":true, "bar"=false}))
 */', '
/**
 * @Foo(foo = "foo", "bar" = @Bar\Baz({"foo":true, "bar"=false}))
 */'),
            array('
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
 */'),
            array('
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
 */'),
        ));
    }

    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider getFixAroundArrayAssignmentsOnlyCases
     */
    public function testFixAroundArrayAssignmentsOnly($expected, $input = null)
    {
        $this->fixer->configure(array(
            'around_parentheses' => false,
            'around_commas' => false,
            'around_argument_assignments' => false,
        ));
        $this->doTest($expected, $input);

        $this->fixer->configure(array(
            'around_parentheses' => false,
            'around_commas' => false,
            'around_argument_assignments' => false,
            'around_array_assignments' => true,
        ));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixAroundArrayAssignmentsOnlyCases()
    {
        return $this->createTestCases(array(
            array('
/**
 * @Foo (foo="foo", bar="bar")
 */'),
            array('
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo(foo = "foo", bar = "bar")
 */'),
            array('
/**
 * @Foo(
 *     foo = "foo",
 *     bar = "bar"
 * )
 */'),
            array('
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"})
 */', '
/**
 * @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"})
 */'),
            array(
                '/** @Foo(foo = "foo", bar = {"foo" : "foo", "bar" = "bar"}) */',
                '/** @Foo(foo = "foo", bar = {"foo" : "foo", "bar"="bar"}) */',
            ),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(foo = "foo", "bar" = @Bar\Baz({"foo" : true, "bar" = false}))
 */', '
/**
 * @Foo(foo = "foo", "bar" = @Bar\Baz({"foo":true, "bar"=false}))
 */'),
            array('
/**
 * Description with a single " character.
 *
 * @Foo(foo = "string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'),
            array('
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
 */'),
        ));
    }

    /**
     * @return array
     */
    public function getInvalidConfigurationCases()
    {
        return array_merge(parent::getInvalidConfigurationCases(), array(
            array(array(
                'around_parentheses' => false,
                'around_commas' => false,
                'around_argument_assignments' => false,
                'around_array_assignments' => false,
            )),
        ));
    }
}
