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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer
 */
final class PhpdocTrimFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                                <?php
                    /**
                     * @param EngineInterface $templating
                     *
                     * @return void
                     */

                EOD
        ];

        yield [
            '<?php

/**
 * @return int количество деактивированных
 */
function deactivateCompleted()
{
    return 0;
}',
        ];

        yield [
            mb_convert_encoding('
<?php
/**
 * Test à
 */
function foo(){}
', 'Windows-1252', 'UTF-8'),
        ];
    }

    public function testFixMore(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 * @internal
                 *@param string $foo
                 *@throws Exception
                 *
                *
                 *
                 *  @return bool
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 *
              *
                 * Hello there!
                 * @internal
                 *@param string $foo
                 *@throws Exception
                 *
                *
                 *
                 *  @return bool
                 *
                 *
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testClassDocBlock(): void
    {
        $expected = <<<'EOD'
            <?php

            namespace Foo;

              /**
             * This is a class that does classy things.
             *
             * @internal
             *
             * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
             * @author Graham Campbell <hello@gjcampbell.co.uk>
               */
            class Bar {}

            EOD;

        $input = <<<'EOD'
            <?php

            namespace Foo;

              /**
               *
             *
             * This is a class that does classy things.
             *
             * @internal
             *
             * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
             * @author Graham Campbell <hello@gjcampbell.co.uk>
             *
                *
              *
               */
            class Bar {}

            EOD;

        $this->doTest($expected, $input);
    }

    public function testEmptyDocBlock(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 *
                 */

            EOD;

        $this->doTest($expected);
    }

    public function testEmptyLargerEmptyDocBlock(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 *
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 *
                 *
                 *
                 *
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testSuperSimpleDocBlockStart(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Test.
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 *
                 * Test.
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testSuperSimpleDocBlockEnd(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Test.
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Test.
                 *
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testWithLinesWithoutAsterisk(): void
    {
        $expected = <<<'EOD'
            <?php

            /**
             * Foo
                  Baz
             */
            class Foo
            {
            }

            EOD;

        $this->doTest($expected);
    }
}
