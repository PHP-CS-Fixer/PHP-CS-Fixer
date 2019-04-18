<?php

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
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer
 */
final class SingleTraitInsertPerStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'simple' => [
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
            ],
            'simple I' => [
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
            ],
            'simple II' => [
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
            ],
            'multiple' => [
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
            ],
            'namespaces' => [
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
            ],
            'comments' => [
                '<?php
class ZZ
{#1
use#2
Z/* 2 */ #3
#4
;use #5
#6
T#7
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
            ],
            'two classes. same file' => [
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
            ],
            'do not fix group' => [
                '<?php
                class Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}',
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     */
    public function testAnonymousClassFixing()
    {
        $this->doTest(
            '<?php new class { use A;use B;}?>',
            '<?php new class { use A, B;}?>'
        );
    }
}
