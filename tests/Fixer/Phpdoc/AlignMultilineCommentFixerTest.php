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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer
 */
final class AlignMultilineCommentFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure(['a' => 1]);
    }

    /**
     * @dataProvider provideDefaultCases
     */
    public function testDefaults(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideDefaultCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                $a = 1;
                    /**
                     * Doc comment
                     *
                     *
                     *
                     * first without an asterisk
                     * second without an asterisk or space
                     */
                EOD,
            <<<'EOD'
                <?php
                $a = 1;
                    /**
                     * Doc comment
                       *

                *
                    first without an asterisk
                second without an asterisk or space
                   */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /**
                     * Document start
                     */
                EOD,
            <<<'EOD'
                <?php
                    /**
                * Document start
                    */
                EOD,
        ];

        yield [
            "<?php\n \n /**\n  * Two new lines\n  */",
            "<?php\n \n /**\n* Two new lines\n*/",
        ];

        yield [
            <<<EOD
                <?php
                \t/**
                \t * Tabs as indentation
                \t */
                EOD,
            <<<EOD
                <?php
                \t/**
                * Tabs as indentation
                        */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                $a = 1;
                /**
                 * Doc command without prior indentation
                 */
                EOD,
            <<<'EOD'
                <?php
                $a = 1;
                /**
                * Doc command without prior indentation
                */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Doc command without prior indentation
                 * Document start
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                * Doc command without prior indentation
                * Document start
                */
                EOD,
        ];

        // Untouched cases
        yield [
            <<<'EOD'
                <?php
                    /*
                     * Multiline comment
                       *
                *
                   */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /** inline doc comment */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                 $a=1;  /**
                *
                 doc comment that doesn't start in a new line

                */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    # Hash single line comments are untouched
                     #
                   #
                      #
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    // Slash single line comments are untouched
                     //
                   //
                      //
                EOD,
        ];

        yield 'uni code test' => [
            <<<'EOD'
                <?php
                class A
                {
                    /**
                     * @SWG\Get(
                     *     path="/api/v0/cards",
                     *     operationId="listCards",
                     *     tags={"Банковские карты"},
                     *     summary="Возвращает список банковских карт."
                     *  )
                     */
                    public function indexAction()
                    {
                    }
                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideDocLikeMultilineCommentsCases
     */
    public function testDocLikeMultilineComments(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['comment_type' => 'phpdocs_like']);
        $this->doTest($expected, $input);
    }

    public static function provideDocLikeMultilineCommentsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    /*
                     * Doc-like Multiline comment
                     *
                     *
                     */
                EOD,
            <<<'EOD'
                <?php
                    /*
                     * Doc-like Multiline comment
                       *
                *
                   */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * Multiline comment with mixed content
                       *
                  Line without an asterisk
                *
                   */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    /*
                     * Two empty lines
                        *


                *
                   */
                EOD,
        ];
    }

    /**
     * @dataProvider provideMixedContentMultilineCommentsCases
     */
    public function testMixedContentMultilineComments(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['comment_type' => 'all_multiline']);
        $this->doTest($expected, $input);
    }

    public static function provideMixedContentMultilineCommentsCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                    /*
                     * Multiline comment with mixed content
                     *
                  Line without an asterisk
                     *
                     */
                EOD,
            <<<'EOD'
                <?php
                    /*
                     * Multiline comment with mixed content
                       *
                  Line without an asterisk
                *
                   */
                EOD,
        ];
    }

    /**
     * @dataProvider provideDefaultCases
     */
    public function testWhitespaceAwareness(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $expected = str_replace("\n", "\r\n", $expected);
        if (null !== $input) {
            $input = str_replace("\n", "\r\n", $input);
        }

        $this->doTest($expected, $input);
    }
}
