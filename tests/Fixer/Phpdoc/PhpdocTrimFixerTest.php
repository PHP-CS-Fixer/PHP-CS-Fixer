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
            <<<'EOF'
                                <?php
                    /**
                     * @param EngineInterface $templating
                     *
                     * @return void
                     */

                EOF
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
        $expected = <<<'EOF'
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

            EOF;

        $input = <<<'EOF'
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

            EOF;

        $this->doTest($expected, $input);
    }

    public function testClassDocBlock(): void
    {
        $expected = <<<'EOF'
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

            EOF;

        $input = <<<'EOF'
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

            EOF;

        $this->doTest($expected, $input);
    }

    public function testEmptyDocBlock(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 *
                 */

            EOF;

        $this->doTest($expected);
    }

    public function testEmptyLargerEmptyDocBlock(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 *
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 *
                 *
                 *
                 *
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testSuperSimpleDocBlockStart(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 * Test.
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 *
                 * Test.
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testSuperSimpleDocBlockEnd(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 * Test.
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 * Test.
                 *
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testWithLinesWithoutAsterisk(): void
    {
        $expected = <<<'EOF'
            <?php

            /**
             * Foo
                  Baz
             */
            class Foo
            {
            }

            EOF;

        $this->doTest($expected);
    }
}
