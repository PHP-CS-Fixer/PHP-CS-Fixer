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
     * @dataProvider provideFixAllCases
     */
    public function testFixAll(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixAllCases
     */
    public function testFixAllWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAll($expected, $input);
    }

    public function provideFixAllCases(): array
    {
        $cases = $this->createTestCases([
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

        $cases[] = [
            '<?php

/**
 * @see \User getId()
 */
',
        ];

        return $cases;
    }

    /**
     * @dataProvider provideFixAroundParenthesesOnlyCases
     */
    public function testFixAroundParenthesesOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixAroundParenthesesOnlyCases
     */
    public function testFixAroundParenthesesOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAroundParenthesesOnly($expected, $input);
    }

    public function provideFixAroundParenthesesOnlyCases(): array
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
     * @dataProvider provideFixAroundCommasOnlyCases
     */
    public function testFixAroundCommasOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixAroundCommasOnlyCases
     */
    public function testFixAroundCommasOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixAroundCommasOnly($expected, $input);
    }

    public function provideFixAroundCommasOnlyCases(): array
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
     * @dataProvider provideFixWithSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceBeforeArgumentAssignmentOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceBeforeArgumentAssignmentOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceBeforeArgumentAssignmentOnly($expected, $input);
    }

    public function provideFixWithSpaceBeforeArgumentAssignmentOnlyCases(): array
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
     * @dataProvider provideFixWithoutSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceBeforeArgumentAssignmentOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithoutSpaceBeforeArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceBeforeArgumentAssignmentOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceBeforeArgumentAssignmentOnly($expected, $input);
    }

    public function provideFixWithoutSpaceBeforeArgumentAssignmentOnlyCases(): array
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
     * @dataProvider provideFixWithSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceAfterArgumentAssignmentOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithSpaceAfterArgumentAssignmentOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceAfterArgumentAssignmentOnly($expected, $input);
    }

    public function provideFixWithSpaceAfterArgumentAssignmentOnlyCases(): array
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
     * @dataProvider provideFixWithoutSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceAfterArgumentAssignmentOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithoutSpaceAfterArgumentAssignmentOnlyCases
     */
    public function testFixWithoutSpaceAfterArgumentAssignmentOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceAfterArgumentAssignmentOnly($expected, $input);
    }

    public function provideFixWithoutSpaceAfterArgumentAssignmentOnlyCases(): array
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
     * @dataProvider provideFixWithSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentEqualOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentEqualOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceBeforeArrayAssignmentEqualOnly($expected, $input);
    }

    public function provideFixWithSpaceBeforeArrayAssignmentEqualOnlyCases(): array
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
     * @dataProvider provideFixWithoutSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentEqualOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithoutSpaceBeforeArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentEqualOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceBeforeArrayAssignmentEqualOnly($expected, $input);
    }

    public function provideFixWithoutSpaceBeforeArrayAssignmentEqualOnlyCases(): array
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
     * @dataProvider provideFixWithSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentEqualOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentEqualOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceAfterArrayAssignmentEqualOnly($expected, $input);
    }

    public function provideFixWithSpaceAfterArrayAssignmentEqualOnlyCases(): array
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
     * @dataProvider provideFixWithoutSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentEqualOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithoutSpaceAfterArrayAssignmentEqualOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentEqualOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceAfterArrayAssignmentEqualOnly($expected, $input);
    }

    public function provideFixWithoutSpaceAfterArrayAssignmentEqualOnlyCases(): array
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
     * @dataProvider provideFixWithSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentColonOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceBeforeArrayAssignmentColonOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceBeforeArrayAssignmentColonOnly($expected, $input);
    }

    public function provideFixWithSpaceBeforeArrayAssignmentColonOnlyCases(): array
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
     * @dataProvider provideFixWithoutSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentColonOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithoutSpaceBeforeArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceBeforeArrayAssignmentColonOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceBeforeArrayAssignmentColonOnly($expected, $input);
    }

    public function provideFixWithoutSpaceBeforeArrayAssignmentColonOnlyCases(): array
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
     * @dataProvider provideFixWithSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentColonOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithSpaceAfterArrayAssignmentColonOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithSpaceAfterArrayAssignmentColonOnly($expected, $input);
    }

    public function provideFixWithSpaceAfterArrayAssignmentColonOnlyCases(): array
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
     * @dataProvider provideFixWithoutSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentColonOnly(string $expected, ?string $input = null): void
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
     * @dataProvider provideFixWithoutSpaceAfterArrayAssignmentColonOnlyCases
     */
    public function testFixWithoutSpaceAfterArrayAssignmentColonOnlyWithDifferentLineEnding(string $expected, ?string $input = null): void
    {
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->testFixWithoutSpaceAfterArrayAssignmentColonOnly($expected, $input);
    }

    public function provideFixWithoutSpaceAfterArrayAssignmentColonOnlyCases(): array
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

    /**
     * @dataProvider provideElementDiscoveringCases
     */
    public function testElementDiscovering(string $element): void
    {
        $this->doTest(
            sprintf('<?php
                class Foo
                {
                    /**
                     * @Foo(foo="foo")
                     */
                    %s
                }
            ', $element),
            sprintf('<?php
                class Foo
                {
                    /**
                     * @Foo(foo = "foo")
                     */
                    %s
                }
            ', $element)
        );
    }

    public static function provideElementDiscoveringCases(): iterable
    {
        yield ['private $foo;'];

        yield ['private string $foo;'];

        yield ['private Foo\Bar $foo;'];

        yield ['private ?Foo\Bar $foo;'];
    }
}
