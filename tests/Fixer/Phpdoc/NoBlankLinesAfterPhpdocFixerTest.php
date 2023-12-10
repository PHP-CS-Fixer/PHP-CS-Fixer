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
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer
 */
final class NoBlankLinesAfterPhpdocFixerTest extends AbstractFixerTestCase
{
    public function testSimpleExampleIsNotChanged(): void
    {
        $input = <<<'EOF'
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

            EOF;

        $this->doTest($input);
    }

    public function testComplexExampleIsNotChanged(): void
    {
        $input = <<<'EOF'
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

            EOF;

        $this->doTest($input);
    }

    public function testCommentsAreNotChanged(): void
    {
        $input = <<<'EOF'
            <?php

            /*
             * This file is part of xyz.
             *
             * License etc...
             */

            namespace Foo\Bar;

            EOF;

        $this->doTest($input);
    }

    public function testLineBeforeDeclareIsNotRemoved(): void
    {
        $expected = <<<'EOF'
            <?php
            /**
             * This is some license header.
             */

            declare(strict_types=1);
            EOF;

        $this->doTest($expected);
    }

    public function testLineBeforeUseStatementIsNotRemoved(): void
    {
        $expected = <<<'EOF'
            <?php
            /**
             * This is some license header.
             */

            use Foo\Bar;
            EOF;

        $this->doTest($expected);
    }

    /**
     * @dataProvider provideLineBeforeIncludeOrRequireIsNotRemovedCases
     */
    public function testLineBeforeIncludeOrRequireIsNotRemoved(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideLineBeforeIncludeOrRequireIsNotRemovedCases(): iterable
    {
        yield [<<<'EOF'
            <?php
            /**
             * This describes what my script does.
             */

            include 'vendor/autoload.php';
            EOF
        ];

        yield [<<<'EOF'
            <?php
            /**
             * This describes what my script does.
             */

            include_once 'vendor/autoload.php';
            EOF
        ];

        yield [<<<'EOF'
            <?php
            /**
             * This describes what my script does.
             */

            require 'vendor/autoload.php';
            EOF
        ];

        yield [<<<'EOF'
            <?php
            /**
             * This describes what my script does.
             */

            require_once 'vendor/autoload.php';
            EOF
        ];
    }

    public function testLineWithSpacesIsRemovedWhenNextTokenIsIndented(): void
    {
        $this->doTest(
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
                class Foo {}'
        );
    }

    public function testLineWithSpacesIsRemovedWhenNextTokenIsNotIndented(): void
    {
        $this->doTest(
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
class Foo {}'
        );
    }

    public function testFixesSimpleClass(): void
    {
        $expected = <<<'EOF'
            <?php

            /**
             * This is the bar class.
             */
            class Bar {}

            EOF;

        $input = <<<'EOF'
            <?php

            /**
             * This is the bar class.
             */


            class Bar {}

            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixesIndentedClass(): void
    {
        $expected = <<<'EOF'
            <?php

                /**
                 *
                 */
                class Foo {
                    private $a;
                }

            EOF;

        $input = <<<'EOF'
            <?php

                /**
                 *
                 */

                class Foo {
                    private $a;
                }

            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixesOthers(): void
    {
        $expected = <<<'EOF'
            <?php

                /**
                 * Constant!
                 */
                const test = 'constant';

                /**
                 * Foo!
                 */
                $foo = 123;

            EOF;

        $input = <<<'EOF'
            <?php

                /**
                 * Constant!
                 */


                const test = 'constant';

                /**
                 * Foo!
                 */

                $foo = 123;

            EOF;

        $this->doTest($expected, $input);
    }

    public function testWhitespaceInDocBlockAboveNamespaceIsNotTouched(): void
    {
        $expected = <<<'EOF'
            <?php

            /**
             * This is a file-level docblock.
             */

            namespace Foo\Bar\Baz;

            EOF;

        $this->doTest($expected);
    }

    public function testFixesWindowsStyle(): void
    {
        $expected = "<?php\r\n    /**     * Constant!     */\n    \$foo = 123;";

        $input = "<?php\r\n    /**     * Constant!     */\r\n\r\n\r\n    \$foo = 123;";

        $this->doTest($expected, $input);
    }

    /**
     * Empty line between typehinting docs and return statement should be preserved.
     *
     * @dataProvider provideInlineTypehintingDocsBeforeFlowBreakCases
     */
    public function testInlineTypehintingDocsBeforeFlowBreak(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideInlineTypehintingDocsBeforeFlowBreakCases(): iterable
    {
        yield [<<<'EOF'
            <?php
            function parseTag($tag)
            {
                $tagClass = get_class($tag);

                if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                    /** @var DocBlock\Tag\VarTag $tag */

                    return $tag->getDescription();
                }
            }
            EOF
        ];

        yield [<<<'EOF'
            <?php
            function parseTag($tag)
            {
                $tagClass = get_class($tag);

                if ('phpDocumentor\Reflection\DocBlock\Tag\VarTag' === $tagClass) {
                    /** @var DocBlock\Tag\VarTag $tag */

                    throw new Exception($tag->getDescription());
                }
            }
            EOF
        ];

        yield [<<<'EOF'
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
            EOF
        ];

        yield [<<<'EOF'
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
            EOF
        ];

        yield [<<<'EOF'
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
            EOF
        ];
    }
}
