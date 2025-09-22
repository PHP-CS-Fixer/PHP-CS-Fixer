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
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
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

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
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

                EOF,
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
            (string) mb_convert_encoding('
<?php
/**
 * Test à
 */
function foo(){}
', 'Windows-1252', 'UTF-8'),
        ];

        yield [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
        ];

        yield [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
        ];

        yield 'empty doc block' => [<<<'EOF'
            <?php
                /**
                 *
                 */

            EOF];

        yield 'empty larger doc block' => [
            <<<'EOF'
                <?php
                    /**
                     *
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     *
                     *
                     *
                     *
                     */

                EOF,
        ];

        yield 'super simple doc block start' => [
            <<<'EOF'
                <?php
                    /**
                     * Test.
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     *
                     * Test.
                     */

                EOF,
        ];

        yield 'super simple doc block end' => [
            <<<'EOF'
                <?php
                    /**
                     * Test.
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Test.
                     *
                     */

                EOF,
        ];

        yield 'with lines without asterisk' => [<<<'EOF'
            <?php

            /**
             * Foo
                  Baz
             */
            class Foo
            {
            }

            EOF];
    }
}
