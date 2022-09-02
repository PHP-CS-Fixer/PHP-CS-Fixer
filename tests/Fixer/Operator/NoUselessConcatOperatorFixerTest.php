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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\NoUselessConcatOperatorFixer
 */
final class NoUselessConcatOperatorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['juggle_simple_strings' => true]);
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        $templateExpected = '<?php $b = %s;';
        $templateInput = '<?php $b = %s.%s;';
        $cases = [
            'single . single' => ["'a'", "'b'", "'ab'"],
            'single . double' => ["'a'", '"b"', '"ab"'],
            'double . single' => ['"b\n"', "'a'", '"b\na"'],
            'double . double' => ['"b"', '"b"', '"bb"'],
            'encapsed . encapsed' => ['"{$a}"', '"{$b}"', '"{$a}{$b}"'],
            'encapsed space. encapsed' => ['"{$a1} "', '"{$b2}"', '"{$a1} {$b2}"'],
            'encapsed . space encapsed' => ['"{$a}"', '" {$b}"', '"{$a} {$b}"'],
            'encapsed space. space encapsed' => ['"{$a} "', '" {$b}"', '"{$a}  {$b}"'],
            'encapsed . single' => ['"{$a}"', "'Z'", '"{$a}Z"'],
            'single . encapsed' => ["'Y'", '"{$a}"', '"Y{$a}"'],
            'encapsed spaced. single' => ['"{$a}   "', "'G'", '"{$a}   G"'],
            'single . space encapsed' => ["'V'", '" {$a}"', '"V {$a}"'],
            'encapsed . double' => ['"{$a} "', '"XX"', '"{$a} XX"'],
            'double . encapsed' => ['"XCV"', '"{$a}"', '"XCV{$a}"'],
            'encapsed spaced . double' => ['"{$a} V "', '"PO"', '"{$a} V PO"'],
            'double . space encapsed' => ['"DSA"', '"   XX {$a}"', '"DSA   XX {$a}"'],
        ];

        foreach ($cases as $label => $case) {
            yield $label => [
                sprintf($templateExpected, $case[2]),
                sprintf($templateInput, $case[0], $case[1]),
            ];
        }

        yield 'encapsed followed by simple double quoted 1' => [
            '<?php echo "Hello, {$fruit}s.";',
            '<?php echo \'Hello,\'  .  " {$fruit}s.";',
        ];

        yield 'encapsed followed by simple double quoted 2' => [
            '<?php echo "Hello.He drank some juice made of {$fruit}s.Bye $user!" /*1*//*2*/ /*3*//*4*/;',
            '<?php echo \'Hello.\' /*1*/ . /*2*/ "He drank some juice made of {$fruit}s."/*3*/./*4*/"Bye $user!";',
        ];

        yield [
            '<?php
$string = "foobar";
echo "char @ -4 \"[$string[-2]]\"!";
',
            '<?php
$string = "foobar";
echo "char @ -4 \"[$string[-2]"."]\"!";
',
        ];

        yield 'double quote concat double quote + comment' => [
            '<?php $fi = "lk" /* 1 *//* 2 */ ;',
            '<?php $fi = "l" /* 1 */ . /* 2 */ "k";',
        ];

        yield 'empty concat empty' => [
            '<?php $aT = "";',
            '<?php $aT = ""."";',
        ];

        yield 'multiple fixes' => [
            '<?php $f0 = "abc | defg";',
            '<?php $f0 = "a"."b"."c | "."d"."e"."f"."g";',
        ];

        yield 'linebreak with indent inside' => [
            '<?php
$text1 = "intro:   |   |"."
               line 2 indent";',
            '<?php
$text1 = "intro:   |"."   |"."
               line 2 indent"."";',
        ];

        yield 'linebreak with indent inside + comment' => [
            '<?php
$text2 = "intro:      "." #a
               line 2 indent";',
            '<?php
$text2 = "intro:   "."   "." #a
               line 2 indent"."";',
        ];

        yield 'do not fix' => [
            '<?php
                $a0x = $b . "c";
                $a1x = "c" . $b;
                $b2x = foo() . "X";
                $b3x = foo() . \'Y\';
                $b4x = "Z" . foo();
                $b5x = \'b\' . foo();
                $b6x = \'X  \n \' . "\n\t";
                $b7x = "\n\t" . \'X $a\';
                $b7x = "abc". 1;
                $b7x = "def". 1.2;
                // bin string
                $f202 = b"a"."b";
                $f201 = b"a".b"b";
                $f203 = "a".B"b";
                echo b"He drank some juice made of {$fruit}s.".b" Sliced the {$fruit}s.";
            ',
        ];

        yield 'single quote concat single quote but with line break after' => [
            "<?php \$fh = 'x'. // some comment
'y';",
        ];

        yield 'single quote concat single quote but with line break before' => [
            "<?php \$ff = 'x' // some comment
.'y';",
        ];

        yield 'linebreak without indent inside' => [
            '<?php
$text3 = "intro:"."
line 2 indent" ?>',
        ];

        yield 'linebreak before concat + comment' => [
            "<?php
\$a = 'The first line of some block.'
.'The second line' // some comment about this line
.'3rd line'
;
",
        ];
    }

    public function testWithConfigJuggling(): void
    {
        $input = '<?php $a = "x" . \'y\';';
        $expected = '<?php $a = "xy";';

        $this->fixer->configure(['juggle_simple_strings' => true]);
        $this->doTest($expected, $input);

        $this->fixer->configure(['juggle_simple_strings' => false]);
        $this->doTest($input);
    }
}
