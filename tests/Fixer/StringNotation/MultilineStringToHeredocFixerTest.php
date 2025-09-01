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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\MultilineStringToHeredocFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\StringNotation\MultilineStringToHeredocFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class MultilineStringToHeredocFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'empty string' => [
            '<?php $a = \'\';',
        ];

        yield 'single line string' => [
            '<?php $a = \'a b\';',
        ];

        yield 'single line string with "\n"' => [
            '<?php $a = \'a\nb\';',
        ];

        yield 'simple single quoted' => [
            <<<'EOF'
                <?php
                $a = <<<'EOD'
                line1
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = 'line1
                line2';
                EOD,
        ];

        yield 'simple double quoted' => [
            <<<'EOF'
                <?php
                $a = <<<EOD
                line1
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = "line1
                line2";
                EOD,
        ];

        yield 'colliding closing marker - one' => [
            <<<'EOF'
                <?php
                $a = <<<'EOD_'
                line1
                EOD
                line2
                EOD_;
                EOF,
            <<<'EOF'
                <?php
                $a = 'line1
                EOD
                line2';
                EOF,
        ];

        yield 'colliding closing marker - two' => [
            <<<'EOF'
                <?php
                $a = <<<'EOD__'
                line1
                EOD
                EOD_
                line2
                EOD__;
                EOF,
            <<<'EOF'
                <?php
                $a = 'line1
                EOD
                EOD_
                line2';
                EOF,
        ];

        yield 'single quoted unescape' => [
            <<<'EOF'
                <?php
                $a = <<<'EOD'
                line1
                \
                \n
                '
                \\'
                \"
                \

                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = 'line1
                \\
                \n
                \'
                \\\\\'
                \"
                \
                ';
                EOD,
        ];

        yield 'double quoted unescape' => [
            <<<'EOF'
                <?php
                $a = <<<EOD
                line1
                \\
                \n
                "
                \\\\"
                \'
                \
                "{$rawPath}"

                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = "line1
                \\
                \n
                \"
                \\\\\"
                \'
                \
                \"{$rawPath}\"
                ";
                EOD,
        ];

        yield 'single quoted /w variable' => [
            <<<'EOF'
                <?php
                $a = <<<'EOD'
                line1$var
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = 'line1$var
                line2';
                EOD,
        ];

        yield 'double quoted /w simple variable' => [
            <<<'EOF'
                <?php
                $a = <<<EOD
                line1$var
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = "line1$var
                line2";
                EOD,
        ];

        yield 'double quoted /w simple curly variable' => [
            <<<'EOF'
                <?php
                $a = <<<EOD
                line1{$var}
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = "line1{$var}
                line2";
                EOD,
        ];

        yield 'double quoted /w complex curly variable' => [
            <<<'EOF'
                <?php
                $a = <<<EOD
                {$arr['foo'][3]}
                { $obj->values[3]->name }
                {${getName()}}
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = "{$arr['foo'][3]}
                { $obj->values[3]->name }
                {${getName()}}";
                EOD,
        ];

        yield 'test stateful fixing loop' => [
            <<<'EOF'
                <?php
                <<<EOD
                $a
                {$b['x']}
                EOD;
                <<<'EOD'
                c
                d
                EOD;

                <<<EOD
                $a
                $b
                EOD;
                <<<EOD
                $c
                $d
                EOD;

                'a';
                <<<'EOD'
                b
                c
                EOD;

                <<<'EOD'
                EOD;
                <<<EOD
                $a $b
                EOD;
                <<<'EOD'
                c d
                EOD;
                <<<EOD
                $a $b
                EOD;
                <<<EOD
                $a
                $b
                EOD;
                <<<'EOD'
                $c
                $d
                EOD;
                EOF,
            <<<'EOF'
                <?php
                "$a
                {$b['x']}";
                'c
                d';

                "$a
                $b";
                "$c
                $d";

                'a';
                'b
                c';

                <<<'EOD'
                EOD;
                <<<EOD
                $a $b
                EOD;
                <<<'EOD'
                c d
                EOD;
                <<<EOD
                $a $b
                EOD;
                <<<EOD
                $a
                $b
                EOD;
                <<<'EOD'
                $c
                $d
                EOD;
                EOF,
        ];

        yield 'simple strings prefixed with b/B' => [
            <<<'EOF'
                <?php
                $a = <<<'EOD'
                line1
                line2
                EOD;
                $b = <<<EOD
                line1
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = b'line1
                line2';
                $b = B"line1
                line2";
                EOD,
        ];

        yield 'double quoted /w simple variable prefixed with b/B' => [
            <<<'EOF'
                <?php
                $a = <<<EOD
                line1$var
                line2
                EOD;
                $b = <<<EOD
                line1$var
                line2
                EOD;
                EOF,
            <<<'EOD'
                <?php
                $a = b"line1$var
                line2";
                $b = B"line1$var
                line2";
                EOD,
        ];
    }
}
