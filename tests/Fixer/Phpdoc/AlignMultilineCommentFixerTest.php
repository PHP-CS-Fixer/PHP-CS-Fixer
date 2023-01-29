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

    public static function provideDefaultCases(): array
    {
        return [
            [
                '<?php
$a = 1;
    /**
     * Doc comment
     *
     *
     *
     * first without an asterisk
     * second without an asterisk or space
     */',
                '<?php
$a = 1;
    /**
     * Doc comment
       *

*
    first without an asterisk
second without an asterisk or space
   */',
            ],
            [
                '<?php
    /**
     * Document start
     */',
                '<?php
    /**
* Document start
    */',
            ],
            [
                "<?php\n \n /**\n  * Two new lines\n  */",
                "<?php\n \n /**\n* Two new lines\n*/",
            ],
            [
                "<?php
\t/**
\t * Tabs as indentation
\t */",
                "<?php
\t/**
* Tabs as indentation
        */",
            ],
            [
                '<?php
$a = 1;
/**
 * Doc command without prior indentation
 */',
                '<?php
$a = 1;
/**
* Doc command without prior indentation
*/',
            ],
            [
                '<?php
/**
 * Doc command without prior indentation
 * Document start
 */',
                '<?php
/**
* Doc command without prior indentation
* Document start
*/',
            ],

            // Untouched cases
            [
                '<?php
    /*
     * Multiline comment
       *
*
   */',
            ],
            [
                '<?php
    /** inline doc comment */',
            ],
            [
                '<?php
 $a=1;  /**
*
 doc comment that doesn\'t start in a new line

*/',
            ],
            [
                '<?php
    # Hash single line comments are untouched
     #
   #
      #',
            ],
            [
                '<?php
    // Slash single line comments are untouched
     //
   //
      //',
            ],
            'uni code test' => [
                '<?php
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
}',
            ],
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

    public static function provideDocLikeMultilineCommentsCases(): array
    {
        return [
            [
                '<?php
    /*
     * Doc-like Multiline comment
     *
     *
     */',
                '<?php
    /*
     * Doc-like Multiline comment
       *
*
   */',
            ],
            [
                '<?php
    /*
     * Multiline comment with mixed content
       *
  Line without an asterisk
*
   */',
            ],
            [
                '<?php
    /*
     * Two empty lines
        *


*
   */',
            ],
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

    public static function provideMixedContentMultilineCommentsCases(): array
    {
        return [
            [
                '<?php
    /*
     * Multiline comment with mixed content
     *
  Line without an asterisk
     *
     */',
                '<?php
    /*
     * Multiline comment with mixed content
       *
  Line without an asterisk
*
   */',
            ],
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
