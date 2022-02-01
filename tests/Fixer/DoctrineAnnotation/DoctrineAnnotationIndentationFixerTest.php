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
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer
 */
final class DoctrineAnnotationIndentationFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFixWithUnindentedMixedLines(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'indent_mixed_lines' => false,
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        $cases = $this->createTestCases([
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo
 * @Bar
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 *    @Foo
 *  @Bar
 */'],
            ['
/**
 * @Foo(
 *     foo="foo"
 * )
 */', '
/**
 *     @Foo(
 * foo="foo"
 *     )
 */'],
            ['
/**
 * @Foo(foo="foo", bar={
 *     "foo": 1,
 *     "foobar": 2,
 *     "foobarbaz": 3
 * })
 */', '
/**
 * @Foo(foo="foo", bar={
 *        "foo": 1,
 *     "foobar": 2,
 *  "foobarbaz": 3
 * })
 */'],
            ['
/**
 * @Foo(@Bar({
 *     "FOO": 1,
 *     "BAR": 2,
 *     "BAZ": 3
 * }))
 */', '
/**
 * @Foo(@Bar({
 *   "FOO": 1,
 *   "BAR": 2,
 *   "BAZ": 3
 * }))
 */'],
            ['
/**
 * @Foo(
 *     @Bar({
 *         "FOO": 1,
 *         "BAR": 2,
 *         "BAZ": 3
 *     })
 * )
 */', '
/**
 * @Foo(
 *  @Bar({
 *   "FOO": 1,
 *   "BAR": 2,
 *   "BAZ": 3
 *  })
 * )
 */'],
            ['
/**
 * @Foo(
 *   @Bar(
 *  "baz"
 * )
 */'],
            ['
/**
 *  Foo(
 *      Bar()
 *      "baz"
 *  )
 */'],
            ['
/**
 * @Foo(  @Bar(
 *     "baz"
 * ) )
 */', '
/**
 * @Foo(  @Bar(
 *  "baz"
 * ) )
 */'],
            ['
/**
 * @Foo(x={
 *     @Bar
 * })
 * @Foo\z
 */', '
/**
 * @Foo(x={
 * @Bar
 * })
 * @Foo\z
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo(
 *     "string "" with inner quote"
 * )
 *
 * @param mixed description with a single " character.
 */', '
/**
 * Description with a single " character.
 *
 * @Foo(
 *  "string "" with inner quote"
 * )
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * @Foo(@Bar,
 * @Baz)
 * @Qux
 */', '
/**
 *  @Foo(@Bar,
 *   @Baz)
 *    @Qux
 */'],
            ['
/**
 * @Foo({"bar",
 * "baz"})
 * @Qux
 */', '
/**
 *  @Foo({"bar",
 *   "baz"})
 *    @Qux
 */'],
            ['
/**
 * // PHPDocumentor 1
 *     @abstract
 *     @access
 *     @code
 *     @deprec
 *     @encode
 *     @exception
 *     @final
 *     @ingroup
 *     @inheritdoc
 *     @inheritDoc
 *     @magic
 *     @name
 *     @toc
 *     @tutorial
 *     @private
 *     @static
 *     @staticvar
 *     @staticVar
 *     @throw
 *
 * // PHPDocumentor 2
 *     @api
 *     @author
 *     @category
 *     @copyright
 *     @deprecated
 *     @example
 *     @filesource
 *     @global
 *     @ignore
 *     @internal
 *     @license
 *     @link
 *     @method
 *     @package
 *     @param
 *     @property
 *     @property-read
 *     @property-write
 *     @return
 *     @see
 *     @since
 *     @source
 *     @subpackage
 *     @throws
 *     @todo
 *     @TODO
 *     @usedBy
 *     @uses
 *     @var
 *     @version
 *
 * // PHPUnit
 *     @after
 *     @afterClass
 *     @backupGlobals
 *     @backupStaticAttributes
 *     @before
 *     @beforeClass
 *     @codeCoverageIgnore
 *     @codeCoverageIgnoreStart
 *     @codeCoverageIgnoreEnd
 *     @covers
 *     @coversDefaultClass
 *     @coversNothing
 *     @dataProvider
 *     @depends
 *     @expectedException
 *     @expectedExceptionCode
 *     @expectedExceptionMessage
 *     @expectedExceptionMessageRegExp
 *     @group
 *     @large
 *     @medium
 *     @preserveGlobalState
 *     @requires
 *     @runTestsInSeparateProcesses
 *     @runInSeparateProcess
 *     @small
 *     @test
 *     @testdox
 *     @ticket
 *     @uses
 *
 * // PHPCheckStyle
 *     @SuppressWarnings
 *
 * // PHPStorm
 *     @noinspection
 *
 * // PEAR
 *     @package_version
 *
 * // PlantUML
 *     @enduml
 *     @startuml
 *
 * // other
 *     @fix
 *     @FIXME
 *     @fixme
 *     @override
 */'],
            ['
/**
 * @Foo({
 * @Bar()}
 * )
 */', '
/**
 * @Foo({
 *     @Bar()}
 * )
 */'],
            ['
/**
* @Foo(foo={
*     "bar": 1,
* }, baz={
*     "qux": 2,
* })
*/
'],
            ['
/**
* @Foo(foo={
*     "foo",
* }, bar={ "bar" }, baz={
*     "baz"
* })
*/
'],
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
     * @dataProvider provideFixWithIndentedMixedLinesCases
     */
    public function testFixWithIndentedMixedLines(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'indent_mixed_lines' => true,
        ]);
        $this->doTest($expected, $input);
    }

    public function provideFixWithIndentedMixedLinesCases(): array
    {
        return $this->createTestCases([
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Foo
 * @Bar
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 *    @Foo
 *  @Bar
 */'],
            ['
/**
 * @Foo(
 *     foo="foo"
 * )
 */', '
/**
 *     @Foo(
 * foo="foo"
 *     )
 */'],
            ['
/**
 * @Foo(foo="foo", bar={
 *     "foo": 1,
 *     "foobar": 2,
 *     "foobarbaz": 3
 * })
 */', '
/**
 * @Foo(foo="foo", bar={
 *        "foo": 1,
 *     "foobar": 2,
 *  "foobarbaz": 3
 * })
 */'],
            ['
/**
 * @Foo(@Bar({
 *     "FOO": 1,
 *     "BAR": 2,
 *     "BAZ": 3
 * }))
 */', '
/**
 * @Foo(@Bar({
 *   "FOO": 1,
 *   "BAR": 2,
 *   "BAZ": 3
 * }))
 */'],
            ['
/**
 * @Foo(
 *     @Bar({
 *         "FOO": 1,
 *         "BAR": 2,
 *         "BAZ": 3
 *     })
 * )
 */', '
/**
 * @Foo(
 *  @Bar({
 *   "FOO": 1,
 *   "BAR": 2,
 *   "BAZ": 3
 *  })
 * )
 */'],
            ['
/**
 * @Foo(
 *   @Bar(
 *  "baz"
 * )
 */'],
            ['
/**
 *  Foo(
 *      Bar()
 *      "baz"
 *  )
 */'],
            ['
/**
 * @Foo(  @Bar(
 *     "baz"
 * ) )
 */', '
/**
 * @Foo(  @Bar(
 *  "baz"
 * ) )
 */'],
            ['
/**
 * @Foo(x={
 *     @Bar
 * })
 * @Foo\z
 */', '
/**
 * @Foo(x={
 * @Bar
 * })
 * @Foo\z
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo(
 *     "string "" with inner quote"
 * )
 *
 * @param mixed description with a single " character.
 */', '
/**
 * Description with a single " character.
 *
 * @Foo(
 *  "string "" with inner quote"
 * )
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * @Foo(@Bar,
 *     @Baz)
 * @Qux
 */', '
/**
 *  @Foo(@Bar,
 *   @Baz)
 *    @Qux
 */'],
            ['
/**
 * @Foo({"bar",
 *     "baz"})
 * @Qux
 */', '
/**
 *  @Foo({"bar",
 *   "baz"})
 *    @Qux
 */'],
            ['
/**
 * // PHPDocumentor 1
 *     @abstract
 *     @access
 *     @code
 *     @deprec
 *     @encode
 *     @exception
 *     @final
 *     @ingroup
 *     @inheritdoc
 *     @inheritDoc
 *     @magic
 *     @name
 *     @toc
 *     @tutorial
 *     @private
 *     @static
 *     @staticvar
 *     @staticVar
 *     @throw
 *
 * // PHPDocumentor 2
 *     @api
 *     @author
 *     @category
 *     @copyright
 *     @deprecated
 *     @example
 *     @filesource
 *     @global
 *     @ignore
 *     @internal
 *     @license
 *     @link
 *     @method
 *     @package
 *     @param
 *     @property
 *     @property-read
 *     @property-write
 *     @return
 *     @see
 *     @since
 *     @source
 *     @subpackage
 *     @throws
 *     @todo
 *     @TODO
 *     @usedBy
 *     @uses
 *     @var
 *     @version
 *
 * // PHPUnit
 *     @after
 *     @afterClass
 *     @backupGlobals
 *     @backupStaticAttributes
 *     @before
 *     @beforeClass
 *     @codeCoverageIgnore
 *     @codeCoverageIgnoreStart
 *     @codeCoverageIgnoreEnd
 *     @covers
 *     @coversDefaultClass
 *     @coversNothing
 *     @dataProvider
 *     @depends
 *     @expectedException
 *     @expectedExceptionCode
 *     @expectedExceptionMessage
 *     @expectedExceptionMessageRegExp
 *     @group
 *     @large
 *     @medium
 *     @preserveGlobalState
 *     @requires
 *     @runTestsInSeparateProcesses
 *     @runInSeparateProcess
 *     @small
 *     @test
 *     @testdox
 *     @ticket
 *     @uses
 *
 * // PHPCheckStyle
 *     @SuppressWarnings
 *
 * // PHPStorm
 *     @noinspection
 *
 * // PEAR
 *     @package_version
 *
 * // PlantUML
 *     @enduml
 *     @startuml
 *
 * // other
 *     @fix
 *     @FIXME
 *     @fixme
 *     @override
 */'],
            ['
/**
 * @Foo({
 *     @Bar()}
 * )
 */'],
        ]);
    }
}
