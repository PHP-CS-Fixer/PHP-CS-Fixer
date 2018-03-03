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
 * @covers \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixer
 */
final class DoctrineAnnotationBracesFixerTest extends AbstractDoctrineAnnotationFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithBracesCases
     */
    public function testFixWithBraces($expected, $input = null)
    {
        $this->fixer->configure(array('syntax' => 'with_braces'));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithBracesCases()
    {
        $cases = $this->createTestCases(array(
            array('
/**
 * @Foo()
 */'),
            array('
/**
 * @Foo   ()
 */'),
            array('
/**
 * @Foo
 * (
 * )
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
 * @Foo
 */'),
            array(
                '/** @Foo() */',
                '/** @Foo */',
            ),
            array('
/**
 * @Foo(@Bar())
 */', '
/**
 * @Foo(@Bar)
 */'),
            array('
/**
 * @Foo(
 *     @Bar()
 * )
 */', '
/**
 * @Foo(
 *     @Bar
 * )
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(
 *     @Bar\Baz()
 * )
 */', '
/**
 * @Foo(
 *     @Bar\Baz
 * )
 */'),
            array('
/**
 * @Foo() @Bar\Baz()
 */', '
/**
 * @Foo @Bar\Baz
 */'),
            array('
/**
 * @Foo("@Bar")
 */'),
            array('
/**
 * Description with a single " character.
 *
 * @Foo("string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'),
            array('
/**
 * @Foo(@Bar
 */'),
            array('
/**
 * @Foo())@Bar)
 */', '
/**
 * @Foo)@Bar)
 */'),
            array('
/**
 * See {@link http://help Help} or {@see BarClass} for details.
 */'),
            array('
/**
 * @var int
 */'),
            array('
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
 * // other
 * @fix
 * @FIXME
 * @fixme
 * @fixme: foo
 * @override
 * @todo: foo
 */'),
        ));

        $cases[] = array(
            '<?php

/**
* @see \User getId()
*/
',
        );

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithoutBracesCases
     */
    public function testFixWithoutBraces($expected, $input = null)
    {
        $this->doTest($expected, $input);

        $this->fixer->configure(array('syntax' => 'without_braces'));
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithoutBracesCases()
    {
        $cases = $this->createTestCases(array(
            array('
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
 */'),
            array(
                '/** @Foo */',
                '/** @Foo   () */',
            ),
            array('
/**
 * @Foo("bar")
 */'),
            array('
/**
 * @Foo
 */', '
/**
 * @Foo
 * (
 * )
 */'),
            array('
/**
 * @Foo(@Bar)
 */', '
/**
 * @Foo(@Bar())
 */'),
            array('
/**
 * @Foo(
 *     @Bar
 * )
 */', '
/**
 * @Foo(
 *     @Bar()
 * )
 */'),
            array('
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
 */'),
            array('
/**
 * @Foo(
 *     @Bar\Baz
 * )
 */', '
/**
 * @Foo(
 *     @Bar\Baz()
 * )
 */'),
            array('
/**
 * @Foo @Bar\Baz
 */', '
/**
 * @Foo() @Bar\Baz()
 */'),
            array('
/**
 * @\Foo @\Bar\Baz
 */', '
/**
 * @\Foo() @\Bar\Baz()
 */'),
            array('
/**
 * @Foo("@Bar()")
 */'),
            array('
/**
 * Description with a single " character.
 *
 * @Foo("string "" with inner quote")
 *
 * @param mixed description with a single " character.
 */'),
            array('
/**
 * @Foo(
 */'),
            array('
/**
 * @Foo)
 */'),
            array('
/**
 * @Foo(@Bar()
 */'),
            array('
/**
 * @Foo
 * @Bar
 * @Baz
 */', '
/**
 * @Foo()
 * @Bar()
 * @Baz()
 */'),
            array('
/**
 * @FIXME ()
 * @fixme ()
 * @TODO ()
 * @todo ()
 */'),
            array('
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
 * // other
 * @fix()
 * @FIXME()
 * @fixme()
 * @fixme: foo()
 * @override()
 * @todo: foo()
 */'),
        ));

        $cases[] = array(
            '<?php

/**
* @see \User getId()
*/
',
        );

        return $cases;
    }

    /**
     * @return array
     */
    public function provideInvalidConfigurationCases()
    {
        return array_merge(parent::provideInvalidConfigurationCases(), array(
            array(array('syntax' => 'foo')),
            array(array(
                'syntax' => 'foo',
                'ignored_tags' => array(),
            )),
        ));
    }
}
