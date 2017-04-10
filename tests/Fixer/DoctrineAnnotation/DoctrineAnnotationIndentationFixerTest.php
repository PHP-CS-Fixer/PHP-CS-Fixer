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
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer
 */
final class DoctrineAnnotationIndentationFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @param string      $expected
     * @param string|null $input
     *
     * @dataProvider getFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function getFixCases()
    {
        return array(
            array('
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
 */'),
            array('
/**
 * @Foo(
 *     foo="foo"
 * )
 */', '
/**
 *     @Foo(
 * foo="foo"
 *     )
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(
 *   @Bar(
 *  "baz"
 * )
 */'),
            array('
/**
 *  Foo(
 *      Bar()
 *      "baz"
 *  )
 */'),
            array('
/**
 * @Foo(  @Bar(
 *     "baz"
 * ) )
 */', '
/**
 * @Foo(  @Bar(
 *  "baz"
 * ) )
 */'),
            array('
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
 */'),
            array('
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
 */'),
            array('
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
 */'),
        );
    }
}
