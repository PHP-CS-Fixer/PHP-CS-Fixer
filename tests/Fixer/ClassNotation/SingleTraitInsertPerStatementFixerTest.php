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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class SingleTraitInsertPerStatementFixerTest extends AbstractFixerTestCase
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
        yield 'simple' => [
            '<?php
final class Example
{
    use Foo;use Bar;
}
',
            '<?php
final class Example
{
    use Foo, Bar;
}
',
        ];

        yield 'simple I' => [
            '<?php
final class Example
{
    use Foo;use Bar;
}
',
            '<?php
final class Example
{
    use Foo,Bar;
}
',
        ];

        yield 'simple II' => [
            '<?php
use Foo\Bar, Foo\Bar2; // do not touch

final class Example
{
    use Foo;use Bar ;
}
',
            '<?php
use Foo\Bar, Foo\Bar2; // do not touch

final class Example
{
    use Foo, Bar ;
}
',
        ];

        yield 'simple III' => [
            '<?php
class Example
{
    use Foo;use Bar;

    public function baz() {}
}
',
            '<?php
class Example
{
    use Foo, Bar;

    public function baz() {}
}
',
        ];

        yield 'multiple' => [
            '<?php
final class Example
{
    use Foo;
    use Foo00;use Bar01;
    use Foo10;use Bar11;use Bar110;
    use Foo20;use Bar20;use Bar200;use Bar201;
}
',
            '<?php
final class Example
{
    use Foo;
    use Foo00, Bar01;
    use Foo10, Bar11, Bar110;
    use Foo20, Bar20, Bar200, Bar201;
}
',
        ];

        yield 'multiple_multiline' => [
            '<?php
final class Example
{
    use Foo;
    use Bar;
    use Baz;
}
',
            '<?php
final class Example
{
    use Foo,
        Bar,
        Baz;
}
',
        ];

        yield 'multiple_multiline_with_comment' => [
            '<?php
final class Example
{
    use Foo;
    use Bar;
//        Bazz,
    use Baz;
}
',
            '<?php
final class Example
{
    use Foo,
        Bar,
//        Bazz,
        Baz;
}
',
        ];

        yield 'namespaces' => [
            '<?php
class Z
{
    use X\Y\Z0;use X\Y\Z0;use M;
    use X\Y\Z1;use X\Y\Z1;
}
                ',
            '<?php
class Z
{
    use X\Y\Z0, X\Y\Z0, M;
    use X\Y\Z1, X\Y\Z1;
}
                ',
        ];

        yield 'comments' => [
            '<?php
class ZZ
{#1
use#2
Z/* 2 */ #3
#4
;#5
#6
use T#7
#8
;#9
#10
}
',
            '<?php
class ZZ
{#1
use#2
Z/* 2 */ #3
#4
,#5
#6
T#7
#8
;#9
#10
}
',
        ];

        yield 'two classes. same file' => [
            '<?php
namespace Foo;

class Test1
{
    use A;use B; /** use A2, B2; */
}

?>
<?php

class Test2
{
    use A1;use B1; # use A2, B2;
}
',
            '<?php
namespace Foo;

class Test1
{
    use A, B; /** use A2, B2; */
}

?>
<?php

class Test2
{
    use A1, B1; # use A2, B2;
}
',
        ];

        yield 'do not fix group' => [
            '<?php
                class Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}',
        ];

        yield 'anonymous class' => [
            '<?php new class { use A;use B;}?>',
            '<?php new class { use A, B;}?>',
        ];
    }
}
