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

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Fixer\PhpTag\EchoTagSyntaxFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Michele Locati <michele@locati.it>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\EchoTagSyntaxFixer
 */
final class EchoTagSyntaxFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideLongToShortFormatCases
     */
    public function testLongToShortFormat(string $expected, ?string $input = null, bool $shortenSimpleStatementsOnly = true): void
    {
        $this->fixer->configure([EchoTagSyntaxFixer::OPTION_FORMAT => EchoTagSyntaxFixer::FORMAT_SHORT, EchoTagSyntaxFixer::OPTION_SHORTEN_SIMPLE_STATEMENTS_ONLY => $shortenSimpleStatementsOnly]);
        $this->doTest($expected, $input);
    }

    public static function provideLongToShortFormatCases(): iterable
    {
        yield ['<?= \'Foo\';', '<?php echo \'Foo\';'];

        yield ['<?= \'Foo\';', '<?php print \'Foo\';'];

        yield ['<?= \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'];

        yield ['<?= \'Foo\'; ?> PLAIN TEXT', '<?php print \'Foo\'; ?> PLAIN TEXT'];

        yield ['PLAIN TEXT<?= \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'];

        yield ['PLAIN TEXT<?= \'Foo\'; ?>', 'PLAIN TEXT<?php print \'Foo\'; ?>'];

        yield ['<?= \'Foo\'; ?> <?= \'Bar\'; ?>', '<?php echo \'Foo\'; ?> <?php echo \'Bar\'; ?>'];

        yield ['<?= \'Foo\'; ?> <?= \'Bar\'; ?>', '<?php print \'Foo\'; ?> <?php echo \'Bar\'; ?>'];

        yield ['<?php echo \'Foo\'; someThingElse();'];

        yield ['<?= \'Foo\'; someThingElse();', '<?php echo \'Foo\'; someThingElse();', false];

        yield ['<?=/*this */ /** should be in the result*/ \'Foo\';', '<?php /*this */ /** should be in the result*/ echo \'Foo\';'];

        yield [
            <<<'EOT'
                <?=/*comment*/
                  1
                ?>
                EOT
            ,
            <<<'EOT'
                <?php /*comment*/ echo
                  1
                ?>
                EOT
        ];

        yield [
            <<<'EOT'
                <?=/*comment*/ 1
                ?>
                EOT
            ,
            <<<'EOT'
                <?php
                  /*comment*/ echo 1
                ?>
                EOT
        ];

        yield [
            <<<'EOT'
                <?=/*comment*/
                  1
                ?>
                EOT
            ,
            <<<'EOT'
                <?php
                  /*comment*/
                  echo
                  1
                ?>
                EOT
        ];
    }

    /**
     * @dataProvider provideShortToLongFormatCases
     */
    public function testShortToLongFormat(string $expected, ?string $input, string $function): void
    {
        $this->fixer->configure([EchoTagSyntaxFixer::OPTION_FORMAT => EchoTagSyntaxFixer::FORMAT_LONG, EchoTagSyntaxFixer::OPTION_LONG_FUNCTION => $function]);
        $this->doTest($expected, $input);
    }

    public static function provideShortToLongFormatCases(): iterable
    {
        $cases = [
            ['<?php <fn> 1;', '<?= 1;'],
            ['<?php <fn> 1;', '<?=1;'],
            ['<?php <fn> /**/1;', '<?=/**/1;'],
            ['<?php <fn> /**/ 1;', '<?=/**/ 1;'],
            ['<?php <fn> \'Foo\';', '<?= \'Foo\';'],
            ['<?php <fn> \'Foo\'; ?> PLAIN TEXT', '<?= \'Foo\'; ?> PLAIN TEXT'],
            ['PLAIN TEXT<?php <fn> \'Foo\'; ?>', 'PLAIN TEXT<?= \'Foo\'; ?>'],
            ['<?php <fn> \'Foo\'; ?> <?php <fn> \'Bar\'; ?>', '<?= \'Foo\'; ?> <?= \'Bar\'; ?>'],
            ['<?php <fn> foo();', '<?=foo();'],
        ];

        foreach ([EchoTagSyntaxFixer::LONG_FUNCTION_ECHO, EchoTagSyntaxFixer::LONG_FUNCTION_PRINT] as $fn) {
            foreach ($cases as $case) {
                yield [str_replace('<fn>', $fn, $case[0]), str_replace('<fn>', $fn, $case[1]), $fn];
            }
        }
    }
}
