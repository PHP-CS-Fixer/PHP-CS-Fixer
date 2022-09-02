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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\EmptyLoopConditionFixer
 */
final class EmptyLoopConditionFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFixConfig(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield 'from `for` to `while`' => [
            '<?php
                while (true){ if(foo()) {break;}}
                for(;$i < 1;){ if(foo()) {break;}}',
            '<?php
                for(;;){ if(foo()) {break;}}
                for(;$i < 1;){ if(foo()) {break;}}',
        ];

        yield 'from `do while` to `while`' => [
            '<?php while (true){ if(foo()) {break;}}',
            '<?php do{ if(foo()) {break;}}while(true);',
        ];

        yield 'from `while` to `for`' => [
            '<?php
                for(;;){ if(foo()) {break;}}
                while(false){ echo 1; }
                while($a()) { echo 2; }
            ',
            '<?php
                while(true){ if(foo()) {break;}}
                while(false){ echo 1; }
                while($a()) { echo 2; }
            ',
            ['style' => 'for'],
        ];

        yield 'from `do while` to `for`' => [
            '<?php for(;;){ if(foo()) {break;}}',
            '<?php do{ if(foo()) {break;}}while(true);',
            ['style' => 'for'],
        ];

        yield 'multiple `do while` to `while`' => [
            '<?php while (true){}while (true){}while (true){}while (true){}while (true){}while (true){}',
            '<?php do{}while(true);do{}while(true);do{}while(true);do{}while(true);do{}while(true);do{}while(true);',
        ];

        yield 'multiple nested `do while` to `while`' => [
            '<?php while (true){while (true){while (true){while (true){while (true){while (true){while (true){}}}}}}}',
            '<?php do{do{do{do{do{do{do{}while(true);}while(true);}while(true);}while(true);}while(true);}while(true);}while(true);',
        ];

        // comment cases

        yield 'comment inside empty `for` condition' => [
            '<?php while (true)/* 1 *//* 2 */{}',
            '<?php for(/* 1 */;;/* 2 */){}',
        ];

        yield 'comment following empty `for` condition' => [
            '<?php for(;;)/* 3 */{}',
            '<?php while(true/* 3 */){}',
            ['style' => 'for'],
        ];

        // space cases
        yield 'lot of space' => [
            '<?php while (true){ foo3(); }              ',
            '<?php do{ foo3(); } while(true)            ; ',
        ];

        yield [
            '<?php

while (true) {
    foo1();
}



',
            '<?php

do {
    foo1();
}
while(
true
)
;',
        ];

        // do not fix cases

        yield 'not empty `while` condition' => [
            '<?php while(true === foo()){ if(foo()) {break;}}',
            null,
            ['style' => 'for'],
        ];

        yield 'not empty `for` condition' => [
            '<?php for(;foo();){ if(foo()) {break;}}',
        ];

        yield 'not empty `do while` condition' => [
            '<?php do{ if(foo()) {break;}}while(foo());',
        ];

        yield '`while` false' => [
            '<?php while(false){ if(foo()) {break;}}',
            null,
            ['style' => 'for'],
        ];
    }
}
