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
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixer
 */
final class DoctrineAnnotationBracesFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @dataProvider provideFixWithBracesCases
     */
    public function testFixWithBraces(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['syntax' => 'with_braces']);
        $this->doTest($expected, $input);
    }

    public function provideFixWithBracesCases(): array
    {
        $cases = $this->createTestCases([
            ['
/**
 * @Foo()
 */'],
            ['
/**
 * @Foo   ()
 */'],
            ['
/**
 * @Foo
 * (
 * )
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
 * @Foo
 */'],
            [
                '/** @Foo() */',
                '/** @Foo */',
            ],
            ['
/**
 * @Foo(@Bar())
 */', '
/**
 * @Foo(@Bar)
 */'],
            ['
/**
 * @Foo(
 *     @Bar()
 * )
 */', '
/**
 * @Foo(
 *     @Bar
 * )
 */'],
            ['
/**
 * @Foo(
 *     @Bar(),
 *     "baz"
 * )
 */', '
/**
 * @Foo(
 *     @Bar,
 *     "baz"
 * )
 */'],
            ['
/**
 * @Foo(
 *     @Bar\Baz()
 * )
 */', '
/**
 * @Foo(
 *     @Bar\Baz
 * )
 */'],
            ['
/**
 * @Foo() @Bar\Baz()
 */', '
/**
 * @Foo @Bar\Baz
 */'],
            ['
/**
 * @Foo("@Bar")
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo("string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * @Foo(@Bar
 */'],
            ['
/**
 * @Foo())@Bar)
 */', '
/**
 * @Foo)@Bar)
 */'],
            ['
/**
 * See {@link https://help Help} or {@see BarClass} for details.
 */'],
            ['
/**
 * @var int
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract
 * @access
 * @code
 * @deprec
 * @encode
 * @exception
 * @final
 * @ingroup
 * @inheritdoc
 * @inheritDoc
 * @magic
 * @name
 * @toc
 * @tutorial
 * @private
 * @static
 * @staticvar
 * @staticVar
 * @throw
 *
 * // PHPDocumentor 2
 * @api
 * @author
 * @category
 * @copyright
 * @deprecated
 * @example
 * @filesource
 * @global
 * @ignore
 * @internal
 * @license
 * @link
 * @method
 * @package
 * @param
 * @property
 * @property-read
 * @property-write
 * @return
 * @see
 * @since
 * @source
 * @subpackage
 * @throws
 * @todo
 * @TODO
 * @usedBy
 * @uses
 * @var
 * @version
 *
 * // PHPUnit
 * @after
 * @afterClass
 * @backupGlobals
 * @backupStaticAttributes
 * @before
 * @beforeClass
 * @codeCoverageIgnore
 * @codeCoverageIgnoreStart
 * @codeCoverageIgnoreEnd
 * @covers
 * @coversDefaultClass
 * @coversNothing
 * @dataProvider
 * @depends
 * @expectedException
 * @expectedExceptionCode
 * @expectedExceptionMessage
 * @expectedExceptionMessageRegExp
 * @group
 * @large
 * @medium
 * @preserveGlobalState
 * @requires
 * @runTestsInSeparateProcesses
 * @runInSeparateProcess
 * @small
 * @test
 * @testdox
 * @ticket
 * @uses
 *
 * // PHPCheckStyle
 * @SuppressWarnings
 *
 * // PHPStorm
 * @noinspection
 *
 * // PEAR
 * @package_version
 *
 * // PlantUML
 * @enduml
 * @startuml
 *
 * // Psalm
 * @psalm
 * @psalm-param
 *
 * // PHPStan
 * @phpstan
 * @phpstan-param
 *
 * // other
 * @fix
 * @FIXME
 * @fixme
 * @fixme: foo
 * @override
 * @todo: foo
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
     * @dataProvider provideFixWithoutBracesCases
     */
    public function testFixWithoutBraces(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);

        $this->fixer->configure(['syntax' => 'without_braces']);
        $this->doTest($expected, $input);
    }

    public function provideFixWithoutBracesCases(): array
    {
        $cases = $this->createTestCases([
            ['
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Baz\Bar
 */', '
/**
 * Foo.
 *
 * @author John Doe
 *
 * @Baz\Bar ( )
 */'],
            [
                '/** @Foo */',
                '/** @Foo   () */',
            ],
            ['
/**
 * @Foo("bar")
 */'],
            ['
/**
 * @Foo
 */', '
/**
 * @Foo
 * (
 * )
 */'],
            ['
/**
 * @Foo(@Bar)
 */', '
/**
 * @Foo(@Bar())
 */'],
            ['
/**
 * @Foo(
 *     @Bar
 * )
 */', '
/**
 * @Foo(
 *     @Bar()
 * )
 */'],
            ['
/**
 * @Foo(
 *     @Bar,
 *     "baz"
 * )
 */', '
/**
 * @Foo(
 *     @Bar(),
 *     "baz"
 * )
 */'],
            ['
/**
 * @Foo(
 *     @Bar\Baz
 * )
 */', '
/**
 * @Foo(
 *     @Bar\Baz()
 * )
 */'],
            ['
/**
 * @Foo @Bar\Baz
 */', '
/**
 * @Foo() @Bar\Baz()
 */'],
            ['
/**
 * @\Foo @\Bar\Baz
 */', '
/**
 * @\Foo() @\Bar\Baz()
 */'],
            ['
/**
 * @Foo("@Bar()")
 */'],
            ['
/**
 * Description with a single " character.
 *
 * @Foo("string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'],
            ['
/**
 * @Foo(
 */'],
            ['
/**
 * @Foo)
 */'],
            ['
/**
 * @Foo(@Bar()
 */'],
            ['
/**
 * @Foo
 * @Bar
 * @Baz
 */', '
/**
 * @Foo()
 * @Bar()
 * @Baz()
 */'],
            ['
/**
 * @FIXME ()
 * @fixme ()
 * @TODO ()
 * @todo ()
 */'],
            ['
/**
 * // PHPDocumentor 1
 * @abstract()
 * @access()
 * @code()
 * @deprec()
 * @encode()
 * @exception()
 * @final()
 * @ingroup()
 * @inheritdoc()
 * @inheritDoc()
 * @magic()
 * @name()
 * @toc()
 * @tutorial()
 * @private()
 * @static()
 * @staticvar()
 * @staticVar()
 * @throw()
 *
 * // PHPDocumentor 2
 * @api()
 * @author()
 * @category()
 * @copyright()
 * @deprecated()
 * @example()
 * @filesource()
 * @global()
 * @ignore()
 * @internal()
 * @license()
 * @link()
 * @method()
 * @package()
 * @param()
 * @property()
 * @property-read()
 * @property-write()
 * @return()
 * @see()
 * @since()
 * @source()
 * @subpackage()
 * @throws()
 * @todo()
 * @TODO()
 * @usedBy()
 * @uses()
 * @var()
 * @version()
 *
 * // PHPUnit
 * @after()
 * @afterClass()
 * @backupGlobals()
 * @backupStaticAttributes()
 * @before()
 * @beforeClass()
 * @codeCoverageIgnore()
 * @codeCoverageIgnoreStart()
 * @codeCoverageIgnoreEnd()
 * @covers()
 * @coversDefaultClass()
 * @coversNothing()
 * @dataProvider()
 * @depends()
 * @expectedException()
 * @expectedExceptionCode()
 * @expectedExceptionMessage()
 * @expectedExceptionMessageRegExp()
 * @group()
 * @large()
 * @medium()
 * @preserveGlobalState()
 * @requires()
 * @runTestsInSeparateProcesses()
 * @runInSeparateProcess()
 * @small()
 * @test()
 * @testdox()
 * @ticket()
 * @uses()
 *
 * // PHPCheckStyle
 * @SuppressWarnings()
 *
 * // PHPStorm
 * @noinspection()
 *
 * // PEAR
 * @package_version()
 *
 * // PlantUML
 * @enduml()
 * @startuml()
 *
 * // Psalm
 * @psalm()
 * @psalm-param()
 *
 * // PHPStan
 * @phpstan()
 * @psalm-param()
 *
 *
 * // other
 * @fix()
 * @FIXME()
 * @fixme()
 * @fixme: foo()
 * @override()
 * @todo: foo()
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
}
