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
 * @covers \PhpCsFixer\Doctrine\Annotation\DocLexer
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

    public static function provideFixCases(): iterable
    {
        yield from self::createTestCases([
            [<<<'EOD'

                /**
                 * Foo.
                 *
                 * @author John Doe
                 *
                 * @Foo
                 * @Bar
                 */
                EOD, <<<'EOD'

                /**
                 * Foo.
                 *
                 * @author John Doe
                 *
                 *    @Foo
                 *  @Bar
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(
                 *     foo="foo"
                 * )
                 */
                EOD, <<<'EOD'

                /**
                 *     @Foo(
                 * foo="foo"
                 *     )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(foo="foo", bar={
                 *     "foo": 1,
                 *     "foobar": 2,
                 *     "foobarbaz": 3
                 * })
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(foo="foo", bar={
                 *        "foo": 1,
                 *     "foobar": 2,
                 *  "foobarbaz": 3
                 * })
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(@Bar({
                 *     "FOO": 1,
                 *     "BAR": 2,
                 *     "BAZ": 3
                 * }))
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(@Bar({
                 *   "FOO": 1,
                 *   "BAR": 2,
                 *   "BAZ": 3
                 * }))
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(
                 *     @Bar({
                 *         "FOO": 1,
                 *         "BAR": 2,
                 *         "BAZ": 3
                 *     })
                 * )
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(
                 *  @Bar({
                 *   "FOO": 1,
                 *   "BAR": 2,
                 *   "BAZ": 3
                 *  })
                 * )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(
                 *   @Bar(
                 *  "baz"
                 * )
                 */
                EOD],
            [<<<'EOD'

                /**
                 *  Foo(
                 *      Bar()
                 *      "baz"
                 *  )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(  @Bar(
                 *     "baz"
                 * ) )
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(  @Bar(
                 *  "baz"
                 * ) )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(x={
                 *     @Bar
                 * })
                 * @Foo\z
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(x={
                 * @Bar
                 * })
                 * @Foo\z
                 */
                EOD],
            [<<<'EOD'

                /**
                 * Description with a single " character.
                 *
                 * @Foo(
                 *     "string "" with inner quote"
                 * )
                 *
                 * @param mixed description with a single " character.
                 */
                EOD, <<<'EOD'

                /**
                 * Description with a single " character.
                 *
                 * @Foo(
                 *  "string "" with inner quote"
                 * )
                 *
                 * @param mixed description with a single " character.
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(@Bar,
                 * @Baz)
                 * @Qux
                 */
                EOD, <<<'EOD'

                /**
                 *  @Foo(@Bar,
                 *   @Baz)
                 *    @Qux
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo({"bar",
                 * "baz"})
                 * @Qux
                 */
                EOD, <<<'EOD'

                /**
                 *  @Foo({"bar",
                 *   "baz"})
                 *    @Qux
                 */
                EOD],
            [<<<'EOD'

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
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo({
                 * @Bar()}
                 * )
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo({
                 *     @Bar()}
                 * )
                 */
                EOD],
            [<<<'EOD'

                /**
                * @Foo(foo={
                *     "bar": 1,
                * }, baz={
                *     "qux": 2,
                * })
                */

                EOD],
            [<<<'EOD'

                /**
                * @Foo(foo={
                *     "foo",
                * }, bar={ "bar" }, baz={
                *     "baz"
                * })
                */

                EOD],
        ]);

        yield [
            <<<'EOD'
                <?php

                /**
                 * @see \User getId()
                 */

                EOD,
        ];
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

    public static function provideFixWithIndentedMixedLinesCases(): iterable
    {
        return self::createTestCases([
            [<<<'EOD'

                /**
                 * Foo.
                 *
                 * @author John Doe
                 *
                 * @Foo
                 * @Bar
                 */
                EOD, <<<'EOD'

                /**
                 * Foo.
                 *
                 * @author John Doe
                 *
                 *    @Foo
                 *  @Bar
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(
                 *     foo="foo"
                 * )
                 */
                EOD, <<<'EOD'

                /**
                 *     @Foo(
                 * foo="foo"
                 *     )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(foo="foo", bar={
                 *     "foo": 1,
                 *     "foobar": 2,
                 *     "foobarbaz": 3
                 * })
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(foo="foo", bar={
                 *        "foo": 1,
                 *     "foobar": 2,
                 *  "foobarbaz": 3
                 * })
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(@Bar({
                 *     "FOO": 1,
                 *     "BAR": 2,
                 *     "BAZ": 3
                 * }))
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(@Bar({
                 *   "FOO": 1,
                 *   "BAR": 2,
                 *   "BAZ": 3
                 * }))
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(
                 *     @Bar({
                 *         "FOO": 1,
                 *         "BAR": 2,
                 *         "BAZ": 3
                 *     })
                 * )
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(
                 *  @Bar({
                 *   "FOO": 1,
                 *   "BAR": 2,
                 *   "BAZ": 3
                 *  })
                 * )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(
                 *   @Bar(
                 *  "baz"
                 * )
                 */
                EOD],
            [<<<'EOD'

                /**
                 *  Foo(
                 *      Bar()
                 *      "baz"
                 *  )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(  @Bar(
                 *     "baz"
                 * ) )
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(  @Bar(
                 *  "baz"
                 * ) )
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(x={
                 *     @Bar
                 * })
                 * @Foo\z
                 */
                EOD, <<<'EOD'

                /**
                 * @Foo(x={
                 * @Bar
                 * })
                 * @Foo\z
                 */
                EOD],
            [<<<'EOD'

                /**
                 * Description with a single " character.
                 *
                 * @Foo(
                 *     "string "" with inner quote"
                 * )
                 *
                 * @param mixed description with a single " character.
                 */
                EOD, <<<'EOD'

                /**
                 * Description with a single " character.
                 *
                 * @Foo(
                 *  "string "" with inner quote"
                 * )
                 *
                 * @param mixed description with a single " character.
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo(@Bar,
                 *     @Baz)
                 * @Qux
                 */
                EOD, <<<'EOD'

                /**
                 *  @Foo(@Bar,
                 *   @Baz)
                 *    @Qux
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo({"bar",
                 *     "baz"})
                 * @Qux
                 */
                EOD, <<<'EOD'

                /**
                 *  @Foo({"bar",
                 *   "baz"})
                 *    @Qux
                 */
                EOD],
            [<<<'EOD'

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
                 */
                EOD],
            [<<<'EOD'

                /**
                 * @Foo({
                 *     @Bar()}
                 * )
                 */
                EOD],
        ]);
    }
}
