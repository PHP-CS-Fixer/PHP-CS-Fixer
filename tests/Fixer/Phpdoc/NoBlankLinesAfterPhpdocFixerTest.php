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
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class NoBlankLinesAfterPhpdocFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'simple example is not changed' => [<<<'EOF'
            <?php

            /**
             * This is the bar class.
             */
            class Bar
            {
                /**
                 * @return void
                 */
                public function foo()
                {
                    //
                }
            }

            EOF];

        yield 'complex example is not changed' => [<<<'EOF'
            <?php
            /**
             * This is the hello function.
             * Yeh, this layout should be allowed.
             * We're fixing lines following a docblock.
             */
            function hello($foo) {}
            /**
             * This is the bar class.
             */
            final class Bar
            {
                /**
                 * @return void
                 */
                public static function foo()
                {
                    //
                }

                /**
                 * @return void
                 */
                static private function bar123() {}

                /*
                 * This T_COMMENT should not be moved
                 *
                 * Only T_DOC_COMMENT should be moved
                 */
                final protected
                // mixin' it up a bit
                function baz() {
                }


                /*
                 * This T_COMMENT should not be moved
                 *
                 * Only T_DOC_COMMENT should be moved
                 */

                public function cool() {}

                /**
                 * This is the first docblock
                 *
                 * Not removing blank line here.
                 * No element is being documented
                 */

                /**
                 * Another docblock
                 */
                public function silly() {}
            }

            EOF];

        yield 'comments are not changed' => [<<<'EOF'
            <?php

            /*
             * This file is part of xyz.
             *
             * License etc...
             */

            namespace Foo\Bar;

            EOF];

        yield 'line before declare is not removed' => [<<<'EOF'
            <?php
            /**
             * This is some license header.
             */

            declare(strict_types=1);
            EOF];

        yield 'line before use statement is not removed' => [<<<'EOF'
            <?php
            /**
             * This is some license header.
             */

            use Foo\Bar;
            EOF];

        yield 'line before include is not removed' => [
            <<<'EOF'
                <?php
                /**
                 * This describes what my script does.
                 */

                include 'vendor/autoload.php';
                EOF,
        ];

        yield 'line before include_once is not removed' => [
            <<<'EOF'
                <?php
                /**
                 * This describes what my script does.
                 */

                include_once 'vendor/autoload.php';
                EOF,
        ];

        yield 'line before require is not removed' => [
            <<<'EOF'
                <?php
                /**
                 * This describes what my script does.
                 */

                require 'vendor/autoload.php';
                EOF,
        ];

        yield 'line before require_once is not removed' => [
            <<<'EOF'
                <?php
                /**
                 * This describes what my script does.
                 */

                require_once 'vendor/autoload.php';
                EOF,
        ];

        yield 'line with spaces is removed When next token is indented' => [
            '<?php
                /**
                 * PHPDoc with a line with space
                 */
                class Foo {}',
            '<?php
                /**
                 * PHPDoc with a line with space
                 */
                '.'
                class Foo {}',
        ];

        yield 'line With spaces is removed when next token is not indented' => [
            '<?php
    /**
     * PHPDoc with a line with space
     */
class Foo {}',
            '<?php
    /**
     * PHPDoc with a line with space
     */
    '.'
class Foo {}',
        ];

        yield 'simple class' => [
            <<<'EOF'
                <?php

                /**
                 * This is the bar class.
                 */
                class Bar {}

                EOF,
            <<<'EOF'
                <?php

                /**
                 * This is the bar class.
                 */


                class Bar {}

                EOF,
        ];

        yield 'indented class' => [
            <<<'EOF'
                <?php

                    /**
                     *
                     */
                    class Foo {
                        private $a;
                    }

                EOF,
            <<<'EOF'
                <?php

                    /**
                     *
                     */

                    class Foo {
                        private $a;
                    }

                EOF,
        ];

        yield 'others' => [
            <<<'EOF'
                <?php

                    /**
                     * Constant!
                     */
                    const test = 'constant';

                    /**
                     * Foo!
                     */
                    $foo = 123;

                EOF,
            <<<'EOF'
                <?php

                    /**
                     * Constant!
                     */


                    const test = 'constant';

                    /**
                     * Foo!
                     */

                    $foo = 123;

                EOF,
        ];

        yield 'whitespace in docblock above namespace is not touched' => [<<<'EOF'
            <?php

            /**
             * This is a file-level docblock.
             */

            namespace Foo\Bar\Baz;

            EOF];

        yield 'windows style' => [
            "<?php\r\n    /**     * Constant!     */\n    \$foo = 123;",
            "<?php\r\n    /**     * Constant!     */\r\n\r\n\r\n    \$foo = 123;",
        ];

        yield 'inline typehinting docs before flow break 1' => [
            <<<'EOF'
                <?php
                function parseTag($tag)
                {
                    $tagClass = get_class($tag);

                    if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                        /** @var DocBlock\Tag\VarTag $tag */

                        return $tag->getDescription();
                    }
                }
                EOF,
        ];

        yield 'inline typehinting docs before flow break 2' => [
            <<<'EOF'
                <?php
                function parseTag($tag)
                {
                    $tagClass = get_class($tag);

                    if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                        /** @var DocBlock\Tag\VarTag $tag */

                        throw new Exception($tag->getDescription());
                    }
                }
                EOF,
        ];

        yield 'inline typehinting docs before flow break 3' => [
            <<<'EOF'
                <?php
                function parseTag($tag)
                {
                    $tagClass = get_class($tag);

                    if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                        /** @var DocBlock\Tag\VarTag $tag */

                        goto FOO;
                    }

                FOO:
                }
                EOF,
        ];

        yield 'inline typehinting docs before flow break 4' => [
            <<<'EOF'
                <?php
                function parseTag($tag)
                {
                    while (true) {
                        $tagClass = get_class($tag);

                        if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                            /** @var DocBlock\Tag\VarTag $tag */

                            continue;
                        }
                    }
                }
                EOF,
        ];

        yield 'inline typehinting docs before flow break 5' => [
            <<<'EOF'
                <?php
                function parseTag($tag)
                {
                    while (true) {
                        $tagClass = get_class($tag);

                        if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                            /** @var DocBlock\Tag\VarTag $tag */

                            break;
                        }
                    }
                }
                EOF,
        ];
    }
}
