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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixer
 */
final class SingleSpaceAroundConstructFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideConfigureRejectsInvalidControlStatementCases
     *
     * @param mixed $construct
     */
    public function testConfigureRejectsInvalidControlStatement($construct): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                $construct,
            ],
        ]);
    }

    public static function provideConfigureRejectsInvalidControlStatementCases(): iterable
    {
        yield 'null' => [null];

        yield 'false' => [false];

        yield 'true' => [true];

        yield 'int' => [0];

        yield 'float' => [3.14];

        yield 'array' => [[]];

        yield 'object' => [new \stdClass()];

        yield 'unknown' => ['foo'];
    }

    /**
     * @dataProvider provideFixWithAbstractCases
     */
    public function testFixWithAbstract(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'abstract',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithAbstractCases(): iterable
    {
        yield [
            '<?php abstract class Foo {}; if($a){}',
            '<?php abstract  class Foo {}; if($a){}',
        ];

        yield [
            '<?php abstract class Foo {};',
            '<?php abstract

class Foo {};',
        ];

        yield [
            '<?php abstract /* foo */class Foo {};',
            '<?php abstract/* foo */class Foo {};',
        ];

        yield [
            '<?php abstract /* foo */class Foo {};',
            '<?php abstract  /* foo */class Foo {};',
        ];

        yield [
            '<?php

abstract class Foo
{
    abstract function bar();
}',
            '<?php

abstract class Foo
{
    abstract  function bar();
}',
        ];

        yield [
            '<?php

abstract class Foo
{
    abstract function bar();
}',
            '<?php

abstract class Foo
{
    abstract

function bar();
}',
        ];

        yield [
            '<?php

abstract class Foo
{
    abstract /* foo */function bar();
}',
            '<?php

abstract class Foo
{
    abstract  /* foo */function bar();
}',
        ];

        yield [
            '<?php

abstract class Foo
{
    abstract /* foo */function bar();
}',
            '<?php

abstract class Foo
{
    abstract/* foo */function bar();
}',
        ];
    }

    /**
     * @dataProvider provideFixWithBreakCases
     */
    public function testFixWithBreak(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'break',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithBreakCases(): iterable
    {
        yield [
            '<?php while (true) { break; }',
        ];

        yield [
            '<?php while (true) { break /* foo */; }',
            '<?php while (true) { break/* foo */; }',
        ];

        yield [
            '<?php while (true) { break /* foo */; }',
            '<?php while (true) { break  /* foo */; }',
        ];

        yield [
            '<?php while (true) { break 1; }',
            '<?php while (true) { break  1; }',
        ];

        yield [
            '<?php while (true) { break 1; }',
            '<?php while (true) { break

1; }',
        ];

        yield [
            '<?php while (true) { break /* foo */1; }',
            '<?php while (true) { break/* foo */1; }',
        ];

        yield [
            '<?php while (true) { break /* foo */1; }',
            '<?php while (true) { break  /* foo */1; }',
        ];
    }

    /**
     * @dataProvider provideFixWithAsCases
     *
     * @param array<string, string[]> $config
     */
    public function testFixWithAs(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithAsCases(): iterable
    {
        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach ($foo as$bar) {}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach ($foo as  $bar) {}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach ($foo  as $bar) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach ($foo as

$bar) {}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach ($foo
as $bar) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach ($foo as /* foo */$bar) {}',
            '<?php foreach ($foo as/* foo */$bar) {}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php foreach ($foo/* foo */ as $bar) {}',
            '<?php foreach ($foo/* foo */as $bar) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach ($foo as /* foo */$bar) {}',
            '<?php foreach ($foo as  /* foo */$bar) {}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php foreach ($foo /* foo */ as $bar) {}',
            '<?php foreach ($foo /* foo */    as $bar) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach (range(1, 12) as $num) {}',
            '<?php foreach (range(1, 12)as $num) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach (range(1, 12) as $num) {}',
            '<?php foreach (range(1, 12)   as $num) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach ([1, 2, 3, 4] as $int) {}',
            '<?php foreach ([1, 2, 3, 4]as $int) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php foreach ([1, 2, 3, 4] as $int) {}',
            '<?php foreach ([1, 2, 3, 4]
                as $int) {}',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php

class Foo
{
    use Bar {
        Bar::baz as bar;
    }
}',
            '<?php

class Foo
{
    use Bar {
        Bar::baz as  bar;
    }
}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php

class Foo
{
    use Bar {
        Bar::baz as bar;
    }
}',
            '<?php

class Foo
{
    use Bar {
        Bar::baz as

bar;
    }
}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php

class Foo
{
    use Bar {
        Bar::baz as /* foo */bar;
    }
}',
            '<?php

class Foo
{
    use Bar {
        Bar::baz as/* foo */bar;
    }
}',

            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php

class Foo
{
    use Bar {
        Bar::baz as /* foo */bar;
    }
}',
            '<?php

class Foo
{
    use Bar {
        Bar::baz as  /* foo */bar;
    }
}',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['as'],
            ],
        ];

        yield [
            '<?php
namespace Foo;

use Bar as Baz;

final class Qux extends Baz {}
',
            '<?php
namespace Foo;

use Bar    as Baz;

final class Qux extends Baz {}
',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php
namespace Foo;

use Bar as Baz;

final class Qux extends Baz {}
',
            '<?php
namespace Foo;

use Bar
    as Baz;

final class Qux extends Baz {}
',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php
namespace Foo;

use Bar /** foo */ as Baz;

final class Qux extends Baz {}
',
            '<?php
namespace Foo;

use Bar /** foo */as Baz;

final class Qux extends Baz {}
',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php
class Foo
{
    use Bar {
        Bar::baz as bar;
    }
}
',
            '<?php
class Foo
{
    use Bar {
        Bar::baz    as bar;
    }
}
',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php
class Foo
{
    use Bar {
        Bar::baz as bar;
    }
}
',
            '<?php
class Foo
{
    use Bar {
        Bar::baz
as bar;
    }
}
',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php
class Foo
{
    use Bar {
        Bar::baz/** foo */ as bar;
    }
}
',
            '<?php
class Foo
{
    use Bar {
        Bar::baz/** foo */as bar;
    }
}
',
            [
                'constructs_preceded_by_a_single_space' => ['as'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCaseCases
     */
    public function testFixWithCase(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'case',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithCaseCases(): iterable
    {
        yield [
            '<?php
switch ($i) {
    case $j:
        break;
}',
            '<?php
switch ($i) {
    case$j:
        break;
}',
        ];

        yield [
            '<?php
switch ($i) {
    case 0:
        break;
}',
            '<?php
switch ($i) {
    case  0:
        break;
}',
        ];

        yield [
            '<?php
switch ($i) {
    case 0:
        break;
}',
            '<?php
switch ($i) {
    case

0:
        break;
}',
        ];

        yield [
            '<?php
switch ($i) {
    case /* foo */0:
        break;
}',
            '<?php
switch ($i) {
    case/* foo */0:
        break;
}',
        ];
    }

    /**
     * @dataProvider provideFixWithCatchCases
     */
    public function testFixWithCatch(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'catch',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithCatchCases(): iterable
    {
        yield [
            '<?php try {} catch (\Exception $exception) {}',
            '<?php try {} catch(\Exception $exception) {}',
        ];

        yield [
            '<?php try {} catch (\Exception $exception) {}',
            '<?php try {} catch  (\Exception $exception) {}',
        ];

        yield [
            '<?php try {} catch (\Exception $exception) {}',
            '<?php try {} catch

(\Exception $exception) {}',
        ];

        yield [
            '<?php try {} catch /* foo */(Exception $exception) {}',
            '<?php try {} catch/* foo */(Exception $exception) {}',
        ];

        yield [
            '<?php try {} catch /* foo */(Exception $exception) {}',
            '<?php try {} catch  /* foo */(Exception $exception) {}',
        ];
    }

    /**
     * @dataProvider provideFixWithClassCases
     */
    public function testFixWithClass(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'class',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithClassCases(): iterable
    {
        yield [
            '<?php class Foo {}',
            '<?php class  Foo {}',
        ];

        yield [
            '<?php class Foo {}',
            '<?php class

Foo {}',
        ];

        yield [
            '<?php class /* foo */Foo {}',
            '<?php class  /* foo */Foo {}',
        ];

        yield [
            '<?php class /* foo */Foo {}',
            '<?php class/* foo */Foo {}',
        ];

        yield [
            '<?php $foo = stdClass::class;',
        ];

        yield [
            '<?php $foo = new class {};',
            '<?php $foo = new class  {};',
            ['constructs_followed_by_a_single_space' => ['class']],
        ];

        yield [
            '<?php $foo = new class {};',
            '<?php $foo = new class{};',
            ['constructs_followed_by_a_single_space' => ['class']],
        ];

        yield [
            '<?php $foo = new class /* foo */{};',
            '<?php $foo = new class/* foo */{};',
            ['constructs_followed_by_a_single_space' => ['class']],
        ];

        yield [
            '<?php $foo = new class /* foo */{};',
            '<?php $foo = new class  /* foo */{};',
            ['constructs_followed_by_a_single_space' => ['class']],
        ];

        yield [
            '<?php $foo = new class(){};',
            null,
            ['constructs_followed_by_a_single_space' => ['class']],
        ];

        yield [
            '<?php return
                    $a ? new class(){ public function foo() { echo 1; }}
                    : 1
                ;',
            null,
            ['constructs_followed_by_a_single_space' => ['return']],
        ];
    }

    /**
     * @dataProvider provideFixWithContinueCases
     */
    public function testFixWithContinue(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'continue',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithContinueCases(): iterable
    {
        yield [
            '<?php while (true) { continue; }',
        ];

        yield [
            '<?php while (true) { continue /* foo */; }',
            '<?php while (true) { continue/* foo */; }',
        ];

        yield [
            '<?php while (true) { continue /* foo */; }',
            '<?php while (true) { continue  /* foo */; }',
        ];

        yield [
            '<?php while (true) { continue 1; }',
            '<?php while (true) { continue  1; }',
        ];

        yield [
            '<?php while (true) { continue 1; }',
            '<?php while (true) { continue

1; }',
        ];

        yield [
            '<?php while (true) { continue /* foo*/ 1; }',
            '<?php while (true) { continue  /* foo*/ 1; }',
        ];
    }

    /**
     * @dataProvider provideFixWithConstCases
     */
    public function testFixWithConst(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'const',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithConstCases(): iterable
    {
        yield [
            '<?php class Foo { const FOO = 9000; }',
            '<?php class Foo { const  FOO = 9000; }',
        ];

        yield [
            '<?php class Foo { const FOO = 9000; }',
            '<?php class Foo { const

FOO = 9000; }',
        ];

        yield [
            '<?php class Foo { const /* foo */FOO = 9000; }',
            '<?php class Foo { const/* foo */FOO = 9000; }',
        ];

        yield [
            '<?php class Foo { const /* foo */FOO = 9000; }',
            '<?php class Foo { const  /* foo */FOO = 9000; }',
        ];

        yield ['<?php class Foo {
    const
        FOO = 9000,
        BAR = 10000;
}',
        ];

        yield [
            '<?php
const
    A = 3,
    B = 3
?>',
        ];

        yield [
            '<?php
const A = 3 ?>

<?php
[ ,
,
,$z
] = foo()  ;',
            '<?php
const     A = 3 ?>

<?php
[ ,
,
,$z
] = foo()  ;',
        ];

        yield [
            '<?php
    const A
    =
    1;
',
            '<?php
    const
    A
    =
    1;
',
        ];
    }

    /**
     * @dataProvider provideFixWithConstImportCases
     */
    public function testFixWithConstImport(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'const_import',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithConstImportCases(): iterable
    {
        yield [
            '<?php use const FOO\BAR;',
            '<?php use const  FOO\BAR;',
        ];

        yield [
            '<?php use const FOO\BAR;',
            '<?php use const

FOO\BAR;',
        ];

        yield [
            '<?php use const /* foo */FOO\BAR;',
            '<?php use const/* foo */FOO\BAR;',
        ];

        yield [
            '<?php use const /* foo */FOO\BAR;',
            '<?php use const  /* foo */FOO\BAR;',
        ];
    }

    /**
     * @dataProvider provideFixWithCloneCases
     */
    public function testFixWithClone(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'clone',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithCloneCases(): iterable
    {
        yield [
            '<?php clone $foo;',
            '<?php clone$foo;',
        ];

        yield [
            '<?php clone $foo;',
            '<?php clone  $foo;',
        ];

        yield [
            '<?php clone $foo;',
            '<?php clone

$foo;',
        ];

        yield [
            '<?php clone /* foo */$foo;',
            '<?php clone/* foo */$foo;',
        ];
    }

    /**
     * @dataProvider provideFixWithDoCases
     */
    public function testFixWithDo(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'do',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDoCases(): iterable
    {
        yield [
            '<?php do {} while (true);',
            '<?php do{} while (true);',
        ];

        yield [
            '<?php DO {} while (true);',
            '<?php DO{} while (true);',
        ];

        yield [
            '<?php do {} while (true);',
            '<?php do  {} while (true);',
        ];

        yield [
            '<?php do {} while (true);',
            '<?php do

{} while (true);',
        ];

        yield [
            '<?php do /* foo*/{} while (true);',
            '<?php do/* foo*/{} while (true);',
        ];

        yield [
            '<?php do /* foo*/{} while (true);',
            '<?php do  /* foo*/{} while (true);',
        ];
    }

    /**
     * @dataProvider provideFixWithEchoCases
     */
    public function testFixWithEcho(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'echo',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithEchoCases(): iterable
    {
        yield [
            '<?php echo $foo;',
            '<?php echo$foo;',
        ];

        yield [
            '<?php echo 9000;',
            '<?php echo  9000;',
        ];

        yield [
            '<?php echo 9000;',
            '<?php echo

9000;',
        ];

        yield [
            '<?php ECHO /* foo */9000;',
            '<?php ECHO/* foo */9000;',
        ];
    }

    /**
     * @dataProvider provideFixWithElseCases
     */
    public function testFixWithElse(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'else',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithElseCases(): iterable
    {
        yield [
            '<?php if (true) {} else {}',
            '<?php if (true) {} else{}',
        ];

        yield [
            '<?php if (true) {} else {}',
            '<?php if (true) {} else  {}',
        ];

        yield [
            '<?php if (true) {} else {}',
            '<?php if (true) {} else

{}',
        ];

        yield [
            '<?php if (true) {} else /* foo */{}',
            '<?php if (true) {} else/* foo */{}',
        ];
    }

    /**
     * @dataProvider provideFixWithElseIfCases
     */
    public function testFixWithElseIf(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'elseif',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithElseIfCases(): iterable
    {
        yield [
            '<?php if (true) {} elseif (false) {}',
            '<?php if (true) {} elseif(false) {}',
        ];

        yield [
            '<?php if (true) {} elseif (false) {}',
            '<?php if (true) {} elseif  (false) {}',
        ];

        yield [
            '<?php if (true) {} elseif (false) {}',
            '<?php if (true) {} elseif

(false) {}',
        ];

        yield [
            '<?php if (true) {} elseif /* foo */(false) {}',
            '<?php if (true) {} elseif/* foo */(false) {}',
        ];
    }

    /**
     * @dataProvider provideFixWithExtendsCases
     */
    public function testFixWithExtends(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'extends',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithExtendsCases(): iterable
    {
        yield [
            '<?php class Foo extends \InvalidArgumentException {}',
            '<?php class Foo extends  \InvalidArgumentException {}',
        ];

        yield [
            '<?php class Foo extends \InvalidArgumentException {}',
            '<?php class Foo extends

\InvalidArgumentException {}',
        ];

        yield [
            '<?php class Foo extends /* foo */\InvalidArgumentException {}',
            '<?php class Foo extends/* foo */\InvalidArgumentException {}',
        ];

        yield [
            '<?php class Foo extends /* foo */\InvalidArgumentException {}',
            '<?php class Foo extends  /* foo */\InvalidArgumentException {}',
        ];

        yield [
            '<?php interface Foo extends Bar1 {}',
            '<?php interface Foo extends  Bar1 {}',
        ];

        yield [
            '<?php interface Foo extends Bar2 {}',
            '<?php interface Foo extends

Bar2 {}',
        ];

        yield [
            '<?php interface Foo extends /* foo */Bar3 {}',
            '<?php interface Foo extends/* foo */Bar3 {}',
        ];

        yield [
            '<?php interface Foo extends /* foo */Bar4 {}',
            '<?php interface Foo extends  /* foo */Bar4 {}',
        ];

        yield [
            '<?php interface Foo extends Bar5, Baz, Qux {}',
            '<?php interface Foo extends  Bar5, Baz, Qux {}',
        ];

        yield [
            '<?php interface Foo extends Bar6, Baz, Qux {}',
            '<?php interface Foo extends

Bar6, Baz, Qux {}',
        ];

        yield [
            '<?php interface Foo extends /* foo */Bar7, Baz, Qux {}',
            '<?php interface Foo extends/* foo */Bar7, Baz, Qux {}',
        ];

        yield [
            '<?php interface Foo extends /* foo */Bar8, Baz, Qux {}',
            '<?php interface Foo extends  /* foo */Bar8, Baz, Qux {}',
        ];

        yield [
            '<?php interface Foo extends
    Bar9,
    Baz,
    Qux
{}',
        ];

        yield [
            '<?php $foo = new class extends \InvalidArgumentException {};',
            '<?php $foo = new class extends  \InvalidArgumentException {};',
        ];

        yield [
            '<?php $foo = new class extends \InvalidArgumentException {};',
            '<?php $foo = new class extends

\InvalidArgumentException {};',
        ];

        yield [
            '<?php $foo = new class extends /* foo */\InvalidArgumentException {};',
            '<?php $foo = new class extends/* foo */\InvalidArgumentException {};',
        ];

        yield [
            '<?php $foo = new class extends /* foo */\InvalidArgumentException {};',
            '<?php $foo = new class extends  /* foo */\InvalidArgumentException {};',
        ];
    }

    /**
     * @dataProvider provideFixWithFinalCases
     */
    public function testFixWithFinal(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'final',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithFinalCases(): iterable
    {
        yield [
            '<?php final class Foo {}',
            '<?php final  class Foo {}',
        ];

        yield [
            '<?php final class Foo {}',
            '<?php final

class Foo {}',
        ];

        yield [
            '<?php final /* foo */class Foo {}',
            '<?php final/* foo */class Foo {}',
        ];

        yield [
            '<?php final /* foo */class Foo {}',
            '<?php final  /* foo */class Foo {}',
        ];

        yield [
            '<?php

class Foo
{
    final function bar() {}
}',
            '<?php

class Foo
{
    final  function bar() {}
}',
        ];

        yield [
            '<?php

class Foo
{
    final function bar() {}
}',
            '<?php

class Foo
{
    final

function bar() {}
}',
        ];

        yield [
            '<?php

class Foo
{
    final /* foo */function bar() {}
}',
            '<?php

class Foo
{
    final/* foo */function bar() {}
}',
        ];

        yield [
            '<?php

class Foo
{
    final /* foo */function bar() {}
}',
            '<?php

class Foo
{
    final  /* foo */function bar() {}
}',
        ];
    }

    /**
     * @dataProvider provideFixWithFinallyCases
     */
    public function testFixWithFinally(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'finally',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithFinallyCases(): iterable
    {
        yield [
            '<?php try {} finally {}',
            '<?php try {} finally{}',
        ];

        yield [
            '<?php try {} finally {}',
            '<?php try {} finally  {}',
        ];

        yield [
            '<?php try {} finally {}',
            '<?php try {} finally

{}',
        ];

        yield [
            '<?php try {} finally /* foo */{}',
            '<?php try {} finally/* foo */{}',
        ];

        yield [
            '<?php try {} finally /* foo */{}',
            '<?php try {} finally  /* foo */{}',
        ];
    }

    /**
     * @dataProvider provideFixWithForCases
     */
    public function testFixWithFor(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'for',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithForCases(): iterable
    {
        yield [
            '<?php for ($i = 0; $i < 3; ++$i) {}',
            '<?php for($i = 0; $i < 3; ++$i) {}',
        ];

        yield [
            '<?php for ($i = 0; $i < 3; ++$i) {}',
            '<?php for  ($i = 0; $i < 3; ++$i) {}',
        ];

        yield [
            '<?php for ($i = 0; $i < 3; ++$i) {}',
            '<?php for

($i = 0; $i < 3; ++$i) {}',
        ];

        yield [
            '<?php for /* foo */($i = 0; $i < 3; ++$i) {}',
            '<?php for/* foo */($i = 0; $i < 3; ++$i) {}',
        ];

        yield [
            '<?php for /* foo */($i = 0; $i < 3; ++$i) {}',
            '<?php for  /* foo */($i = 0; $i < 3; ++$i) {}',
        ];
    }

    /**
     * @dataProvider provideFixWithForeachCases
     */
    public function testFixWithForeach(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'foreach',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithForeachCases(): iterable
    {
        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach($foo as $bar) {}',
        ];

        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach  ($foo as $bar) {}',
        ];

        yield [
            '<?php foreach ($foo as $bar) {}',
            '<?php foreach

($foo as $bar) {}',
        ];

        yield [
            '<?php foreach /* foo */($foo as $bar) {}',
            '<?php foreach/* foo */($foo as $bar) {}',
        ];

        yield [
            '<?php foreach /* foo */($foo as $bar) {}',
            '<?php foreach  /* foo */($foo as $bar) {}',
        ];
    }

    /**
     * @dataProvider provideFixWithFunctionCases
     */
    public function testFixWithFunction(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'function',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithFunctionCases(): iterable
    {
        yield [
            '<?php function foo() {}',
            '<?php function  foo() {}',
        ];

        yield [
            '<?php function foo() {}',
            '<?php function

foo() {}',
        ];

        yield [
            '<?php function /* foo */foo() {}',
            '<?php function/* foo */foo() {}',
        ];

        yield [
            '<?php function /* foo */foo() {}',
            '<?php function  /* foo */foo() {}',
        ];

        yield [
            '<?php
class Foo
{
    function bar() {}
}
',
            '<?php
class Foo
{
    function  bar() {}
}
',
        ];

        yield [
            '<?php
class Foo
{
    function bar() {}
}
',
            '<?php
class Foo
{
    function

bar() {}
}
',
        ];

        yield [
            '<?php
class Foo
{
    function /* foo */bar() {}
}
',
            '<?php
class Foo
{
    function/* foo */bar() {}
}
',
        ];

        yield [
            '<?php
class Foo
{
    function /* foo */bar() {}
}
',
            '<?php
class Foo
{
    function  /* foo */bar() {}
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithFunctionImportCases
     */
    public function testFixWithFunctionImport(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'function_import',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithFunctionImportCases(): iterable
    {
        yield [
            '<?php use function Foo\bar;',
            '<?php use function  Foo\bar;',
        ];

        yield [
            '<?php use function Foo\bar;',
            '<?php use function

Foo\bar;',
        ];

        yield [
            '<?php use function /* foo */Foo\bar;',
            '<?php use function/* foo */Foo\bar;',
        ];

        yield [
            '<?php use function /* foo */Foo\bar;',
            '<?php use function  /* foo */Foo\bar;',
        ];
    }

    /**
     * @dataProvider provideFixWithGlobalCases
     */
    public function testFixWithGlobal(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'global',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithGlobalCases(): iterable
    {
        yield [
            '<?php function foo() { global $bar; }',
            '<?php function foo() { global$bar; }',
        ];

        yield [
            '<?php function foo() { global $bar; }',
            '<?php function foo() { global  $bar; }',
        ];

        yield [
            '<?php function foo() { global $bar; }',
            '<?php function foo() { global

$bar; }',
        ];

        yield [
            '<?php function foo() { global /* foo */$bar; }',
            '<?php function foo() { global/* foo */$bar; }',
        ];

        yield [
            '<?php function foo() { global /* foo */$bar; }',
            '<?php function foo() { global  /* foo */$bar; }',
        ];
    }

    /**
     * @dataProvider provideFixWithGotoCases
     */
    public function testFixWithGoto(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'goto',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithGotoCases(): iterable
    {
        yield [
            '<?php goto foo; foo: echo "Bar";',
            '<?php goto  foo; foo: echo "Bar";',
        ];

        yield [
            '<?php goto foo; foo: echo "Bar";',
            '<?php goto

foo; foo: echo "Bar";',
        ];

        yield [
            '<?php goto /* foo */foo; foo: echo "Bar";',
            '<?php goto/* foo */foo; foo: echo "Bar";',
        ];
    }

    /**
     * @dataProvider provideFixWithIfCases
     */
    public function testFixWithIf(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'if',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIfCases(): iterable
    {
        yield [
            '<?php if ($foo === $bar) {}',
            '<?php if($foo === $bar) {}',
        ];

        yield [
            '<?php if ($foo === $bar) {}',
            '<?php if  ($foo === $bar) {}',
        ];

        yield [
            '<?php if ($foo === $bar) {}',
            '<?php if

($foo === $bar) {}',
        ];

        yield [
            '<?php if /* foo */($foo === $bar) {}',
            '<?php if/* foo */($foo === $bar) {}',
        ];
    }

    /**
     * @dataProvider provideFixWithImplementsCases
     */
    public function testFixWithImplements(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'implements',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithImplementsCases(): iterable
    {
        yield [
            '<?php class Foo implements \Countable {}',
            '<?php class Foo implements  \Countable {}',
        ];

        yield [
            '<?php class Foo implements \Countable {}',
            '<?php class Foo implements

\Countable {}',
        ];

        yield [
            '<?php class Foo implements /* foo */\Countable {}',
            '<?php class Foo implements/* foo */\Countable {}',
        ];

        yield [
            '<?php class Foo implements /* foo */\Countable {}',
            '<?php class Foo implements  /* foo */\Countable {}',
        ];

        yield [
            '<?php class Foo implements
                    \Countable,
                    Bar,
                    Baz
                {}',
        ];

        yield [
            '<?php $foo = new class implements \Countable {};',
            '<?php $foo = new class implements  \Countable {};',
        ];

        yield [
            '<?php $foo = new class implements \Countable {};',
            '<?php $foo = new class implements

\Countable {};',
        ];

        yield [
            '<?php $foo = new class implements /* foo */\Countable {};',
            '<?php $foo = new class implements/* foo */\Countable {};',
        ];

        yield [
            '<?php $foo = new class implements /* foo */\Countable {};',
            '<?php $foo = new class implements  /* foo */\Countable {};',
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeCases
     */
    public function testFixWithInclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'include',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIncludeCases(): iterable
    {
        yield [
            '<?php include "vendor/autoload.php";',
            '<?php include"vendor/autoload.php";',
        ];

        yield [
            '<?php include "vendor/autoload.php";',
            '<?php include  "vendor/autoload.php";',
        ];

        yield [
            '<?php include "vendor/autoload.php";',
            '<?php include

"vendor/autoload.php";',
        ];

        yield [
            '<?php include /* foo */"vendor/autoload.php";',
            '<?php include/* foo */"vendor/autoload.php";',
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeOnceCases
     */
    public function testFixWithIncludeOnce(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'include_once',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIncludeOnceCases(): iterable
    {
        yield [
            '<?php include_once "vendor/autoload.php";',
            '<?php include_once"vendor/autoload.php";',
        ];

        yield [
            '<?php include_once "vendor/autoload.php";',
            '<?php include_once  "vendor/autoload.php";',
        ];

        yield [
            '<?php include_once "vendor/autoload.php";',
            '<?php include_once

"vendor/autoload.php";',
        ];

        yield [
            '<?php include_once /* foo */"vendor/autoload.php";',
            '<?php include_once/* foo */"vendor/autoload.php";',
        ];
    }

    /**
     * @dataProvider provideFixWithInstanceofCases
     */
    public function testFixWithInstanceof(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'instanceof',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithInstanceofCases(): iterable
    {
        yield [
            '<?php $foo instanceof \stdClass;',
            '<?php $foo instanceof  \stdClass;',
        ];

        yield [
            '<?php $foo instanceof \stdClass;',
            '<?php $foo instanceof

\stdClass;',
        ];

        yield [
            '<?php $foo instanceof /* foo */\stdClass;',
            '<?php $foo instanceof/* foo */\stdClass;',
        ];

        yield [
            '<?php $foo instanceof /* foo */\stdClass;',
            '<?php $foo instanceof  /* foo */\stdClass;',
        ];

        yield [
            '<?php $foo instanceof $bar;',
            '<?php $foo instanceof$bar;',
        ];
    }

    /**
     * @dataProvider provideFixWithInsteadofCases
     */
    public function testFixWithInsteadof(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'insteadof',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithInsteadofCases(): iterable
    {
        yield [
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}',
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof  A;
        A::bigTalk insteadof B;
    }
}',
        ];

        yield [
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof A;
        A::bigTalk insteadof B;
    }
}',
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof

A;
        A::bigTalk insteadof B;
    }
}',
        ];

        yield [
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof /* foo */A;
        A::bigTalk insteadof B;
    }
}',
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof/* foo */A;
        A::bigTalk insteadof B;
    }
}',
        ];

        yield [
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof /* foo */A;
        A::bigTalk insteadof B;
    }
}',
            '<?php

class Talker {
    use A, B {
        B::smallTalk insteadof  /* foo */A;
        A::bigTalk insteadof B;
    }
}',
        ];
    }

    /**
     * @dataProvider provideFixWithInterfaceCases
     */
    public function testFixWithInterface(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'interface',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithInterfaceCases(): iterable
    {
        yield [
            '<?php interface Foo {}',
            '<?php interface  Foo {}',
        ];

        yield [
            '<?php interface Foo {}',
            '<?php interface

Foo {}',
        ];

        yield [
            '<?php interface /* foo */Foo {}',
            '<?php interface  /* foo */Foo {}',
        ];

        yield [
            '<?php interface /* foo */Foo {}',
            '<?php interface/* foo */Foo {}',
        ];
    }

    /**
     * @dataProvider provideFixWithNewCases
     */
    public function testFixWithNew(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'new',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithNewCases(): iterable
    {
        yield [
            '<?php new $foo();',
            '<?php new$foo();',
        ];

        yield [
            '<?php new Bar();',
            '<?php new  Bar();',
        ];

        yield [
            '<?php new Bar();',
            '<?php new

Bar();',
        ];

        yield [
            '<?php new /* foo */Bar();',
            '<?php new/* foo */Bar();',
        ];
    }

    /**
     * @dataProvider provideFixWithOpenTagWithEchoCases
     */
    public function testFixWithOpenTagWithEcho(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'open_tag_with_echo',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithOpenTagWithEchoCases(): iterable
    {
        yield [
            '<?= $foo ?>',
            '<?=$foo ?>',
        ];

        yield [
            '<?= $foo ?>',
            '<?=  $foo ?>',
        ];

        yield [
            '<?= $foo ?>',
            '<?=

$foo ?>',
        ];

        yield [
            '<?= /* foo */$foo ?>',
            '<?=/* foo */$foo ?>',
        ];

        yield [
            '<?= /* foo */$foo ?>',
            '<?=  /* foo */$foo ?>',
        ];
    }

    /**
     * @dataProvider provideFixWithPrintCases
     */
    public function testFixWithPrint(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'print',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithPrintCases(): iterable
    {
        yield [
            '<?php print $foo;',
            '<?php print$foo;',
        ];

        yield [
            '<?php print 9000;',
            '<?php print  9000;',
        ];

        yield [
            '<?php print 9000;',
            '<?php print

9000;',
        ];

        yield [
            '<?php print /* foo */9000;',
            '<?php print/* foo */9000;',
        ];
    }

    /**
     * @dataProvider provideFixWithPrivateCases
     */
    public function testFixWithPrivate(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'private',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithPrivateCases(): iterable
    {
        yield [
            '<?php class Foo { private $bar; }',
            '<?php class Foo { private$bar; }',
        ];

        yield [
            '<?php class Foo { private $bar; }',
            '<?php class Foo { private  $bar; }',
        ];

        yield [
            '<?php class Foo { private $bar; }',
            '<?php class Foo { private

$bar; }',
        ];

        yield [
            '<?php class Foo { private /* foo */$bar; }',
            '<?php class Foo { private/* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { private /* foo */$bar; }',
            '<?php class Foo { private  /* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { private function bar() {} }',
            '<?php class Foo { private  function bar() {} }',
        ];

        yield [
            '<?php class Foo { private function bar() {} }',
            '<?php class Foo { private

function bar() {} }',
        ];

        yield [
            '<?php class Foo { private /* foo */function bar() {} }',
            '<?php class Foo { private/* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { private /* foo */function bar() {} }',
            '<?php class Foo { private  /* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { private CONST BAR = 9000; }',
            '<?php class Foo { private  CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { private CONST BAR = 9000; }',
            '<?php class Foo { private

CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { private /* foo */CONST BAR = 9000; }',
            '<?php class Foo { private/* foo */CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { private /* foo */CONST BAR = 9000; }',
            '<?php class Foo { private  /* foo */CONST BAR = 9000; }',
        ];
    }

    /**
     * @dataProvider provideFixWithProtectedCases
     */
    public function testFixWithProtected(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'protected',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithProtectedCases(): iterable
    {
        yield [
            '<?php class Foo { protected $bar; }',
            '<?php class Foo { protected$bar; }',
        ];

        yield [
            '<?php class Foo { protected $bar; }',
            '<?php class Foo { protected  $bar; }',
        ];

        yield [
            '<?php class Foo { protected $bar; }',
            '<?php class Foo { protected

$bar; }',
        ];

        yield [
            '<?php class Foo { protected /* foo */$bar; }',
            '<?php class Foo { protected/* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { protected /* foo */$bar; }',
            '<?php class Foo { protected  /* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { protected function bar() {} }',
            '<?php class Foo { protected  function bar() {} }',
        ];

        yield [
            '<?php class Foo { protected function bar() {} }',
            '<?php class Foo { protected

function bar() {} }',
        ];

        yield [
            '<?php class Foo { protected /* foo */function bar() {} }',
            '<?php class Foo { protected/* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { protected /* foo */function bar() {} }',
            '<?php class Foo { protected  /* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { protected CONST BAR = 9000; }',
            '<?php class Foo { protected  CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { protected CONST BAR = 9000; }',
            '<?php class Foo { protected

CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { protected /* foo */CONST BAR = 9000; }',
            '<?php class Foo { protected/* foo */CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { protected /* foo */CONST BAR = 9000; }',
            '<?php class Foo { protected  /* foo */CONST BAR = 9000; }',
        ];
    }

    /**
     * @dataProvider provideFixWithPublicCases
     */
    public function testFixWithPublic(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'public',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithPublicCases(): iterable
    {
        yield [
            '<?php class Foo { public $bar; }',
            '<?php class Foo { public$bar; }',
        ];

        yield [
            '<?php class Foo { Public $bar; }',
            '<?php class Foo { Public  $bar; }',
        ];

        yield [
            '<?php class Foo { public $bar; }',
            '<?php class Foo { public

$bar; }',
        ];

        yield [
            '<?php class Foo { public /* foo */$bar; }',
            '<?php class Foo { public/* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { public /* foo */$bar; }',
            '<?php class Foo { public  /* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { public function bar() {} }',
            '<?php class Foo { public  function bar() {} }',
        ];

        yield [
            '<?php class Foo { public function bar() {} }',
            '<?php class Foo { public

function bar() {} }',
        ];

        yield [
            '<?php class Foo { public /* foo */function bar() {} }',
            '<?php class Foo { public/* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { public /* foo */function bar() {} }',
            '<?php class Foo { public  /* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { public CONST BAR = 9000; }',
            '<?php class Foo { public  CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { public CONST BAR = 9000; }',
            '<?php class Foo { public

CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { public /* foo */CONST BAR = 9000; }',
            '<?php class Foo { public/* foo */CONST BAR = 9000; }',
        ];

        yield [
            '<?php class Foo { public /* foo */CONST BAR = 9000; }',
            '<?php class Foo { public  /* foo */CONST BAR = 9000; }',
        ];
    }

    /**
     * @dataProvider provideFixWithRequireCases
     */
    public function testFixWithRequire(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'require',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithRequireCases(): iterable
    {
        yield [
            '<?php require "vendor/autoload.php";',
            '<?php require"vendor/autoload.php";',
        ];

        yield [
            '<?php require "vendor/autoload.php";',
            '<?php require  "vendor/autoload.php";',
        ];

        yield [
            '<?php require "vendor/autoload.php";',
            '<?php require

"vendor/autoload.php";',
        ];

        yield [
            '<?php require /* foo */"vendor/autoload.php";',
            '<?php require/* foo */"vendor/autoload.php";',
        ];
    }

    /**
     * @dataProvider provideFixWithRequireOnceCases
     */
    public function testFixWithRequireOnce(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'require_once',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithRequireOnceCases(): iterable
    {
        yield [
            '<?php require_once "vendor/autoload.php";',
            '<?php require_once"vendor/autoload.php";',
        ];

        yield [
            '<?php require_once "vendor/autoload.php";',
            '<?php require_once  "vendor/autoload.php";',
        ];

        yield [
            '<?php require_once "vendor/autoload.php";',
            '<?php require_once

"vendor/autoload.php";',
        ];

        yield [
            '<?php require_once /* foo */"vendor/autoload.php";',
            '<?php require_once/* foo */"vendor/autoload.php";',
        ];
    }

    /**
     * @dataProvider provideFixWithReturnCases
     */
    public function testFixWithReturn(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'return',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithReturnCases(): iterable
    {
        yield [
            '<?php return;',
        ];

        yield [
            '<?php return /* foo */;',
            '<?php return/* foo */;',
        ];

        yield [
            '<?php return /* foo */;',
            '<?php return  /* foo */;',
        ];

        yield [
            '<?php return $foo;',
            '<?php return$foo;',
        ];

        yield [
            '<?php return 9000;',
            '<?php return  9000;',
        ];

        yield [
            '<?php return 9000;',
            '<?php return

9000;',
        ];

        yield [
            '<?php return /* */ 9000 + 1 /* foo */       ?>',
            '<?php return





/* */ 9000 + 1 /* foo */       ?>',
        ];

        yield [
            '<?php return /* foo */9000;',
            '<?php return/* foo */9000;',
        ];

        yield [
            '<?php return $foo && $bar || $baz;',
            '<?php return

$foo && $bar || $baz;',
        ];

        yield [
            '<?php

return
    $foo
    && $bar
    || $baz;',
        ];

        yield [
            '<?php

return
    $foo &&
    $bar ||
    $baz;',
        ];

        yield [
            '<?php

return
    $foo
    + $bar
    - $baz;',
        ];

        yield [
            '<?php

return
    $foo +
    $bar -
    $baz;',
        ];

        yield [
            '<?php

return
    $foo ?
    $bar :
    $baz;',
        ];

        yield [
            '<?php

return
    $foo
    ? $bar
    : baz;',
        ];

        yield [
            '<?php

return
    $foo ?:
    $bar;',
        ];

        yield [
            '<?php

return
    $foo
    ?: $bar;',
        ];

        yield [
            '<?php

return
    $foo
    ?: $bar?>',
        ];
    }

    /**
     * @dataProvider provideFixWithStaticCases
     */
    public function testFixWithStatic(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'static',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithStaticCases(): iterable
    {
        yield [
            '<?php function foo() { static $bar; }',
            '<?php function foo() { static$bar; }',
        ];

        yield [
            '<?php function foo() { static $bar; }',
            '<?php function foo() { static  $bar; }',
        ];

        yield [
            '<?php function foo() { static $bar; }',
            '<?php function foo() { static

$bar; }',
        ];

        yield [
            '<?php function foo() { static /* foo */$bar; }',
            '<?php function foo() { static/* foo */$bar; }',
        ];

        yield [
            '<?php function foo() { static /* foo */$bar; }',
            '<?php function foo() { static  /* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { static function bar() {} }',
            '<?php class Foo { static  function bar() {} }',
        ];

        yield [
            '<?php class Foo { static function bar() {} }',
            '<?php class Foo { static

function bar() {} }',
        ];

        yield [
            '<?php class Foo { static /* foo */function bar() {} }',
            '<?php class Foo { static/* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { static /* foo */function bar() {} }',
            '<?php class Foo { static  /* foo */function bar() {} }',
        ];

        yield [
            '<?php class Foo { public static ?int $x; }',
            '<?php class Foo { public static?int $x; }',
        ];

        yield [
            '<?php class Foo { public static ?int $x; }',
            '<?php class Foo { public static   ?int $x; }',
        ];

        yield [
            '<?php class Foo { public static int $x; }',
            '<?php class Foo { public static   int $x; }',
        ];

        yield [
            '<?php class Foo { public static \Closure $a; }',
            '<?php class Foo { public static    \Closure $a; }',
        ];

        yield [
            '<?php class Foo { public static array $c; }',
            '<?php class Foo { public static
array $c; }',
        ];

        yield [
            '<?php $a = static fn(): bool => true;',
            '<?php $a = static    fn(): bool => true;',
        ];

        yield [
            '<?php class Foo { function bar() { return new static(); } }',
        ];

        yield [
            '<?php class Foo { function bar() { return static::class; } }',
        ];
    }

    /**
     * @dataProvider provideFixWithThrowCases
     */
    public function testFixWithThrow(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'throw',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithThrowCases(): iterable
    {
        yield [
            '<?php throw $foo;',
            '<?php throw$foo;',
        ];

        yield [
            '<?php throw new Exception();',
            '<?php throw  new Exception();',
        ];

        yield [
            '<?php throw new Exception();',
            '<?php throw

new Exception();',
        ];

        yield [
            '<?php throw /* foo */new Exception();',
            '<?php throw/* foo */new Exception();',
        ];
    }

    /**
     * @dataProvider provideFixWithTraitCases
     */
    public function testFixWithTrait(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'trait',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithTraitCases(): iterable
    {
        yield [
            '<?php trait Foo {}',
            '<?php trait  Foo {}',
        ];

        yield [
            '<?php trait Foo {}',
            '<?php trait

Foo {}',
        ];

        yield [
            '<?php trait /* foo */Foo {}',
            '<?php trait  /* foo */Foo {}',
        ];

        yield [
            '<?php trait /* foo */Foo {}',
            '<?php trait/* foo */Foo {}',
        ];
    }

    /**
     * @dataProvider provideFixWithTryCases
     */
    public function testFixWithTry(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'try',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithTryCases(): iterable
    {
        yield [
            '<?php try {} catch (\Exception $exception) {}',
            '<?php try{} catch (\Exception $exception) {}',
        ];

        yield [
            '<?php try {} catch (\Exception $exception) {}',
            '<?php try  {} catch (\Exception $exception) {}',
        ];

        yield [
            '<?php try {} catch (\Exception $exception) {}',
            '<?php try

{} catch (\Exception $exception) {}',
        ];

        yield [
            '<?php try /* foo */{} catch (\Exception $exception) {}',
            '<?php try/* foo */{} catch (\Exception $exception) {}',
        ];

        yield [
            '<?php try /* foo */{} catch (\Exception $exception) {}',
            '<?php try  /* foo */{} catch (\Exception $exception) {}',
        ];
    }

    /**
     * @dataProvider provideFixWithUseCases
     */
    public function testFixWithUse(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'use',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithUseCases(): iterable
    {
        yield [
            '<?php use Foo\Bar;',
            '<?php use  Foo\Bar;',
        ];

        yield [
            '<?php use Foo\Bar;',
            '<?php use

Foo\Bar;',
        ];

        yield [
            '<?php use /* foo */Foo\Bar;',
            '<?php use/* foo */Foo\Bar;',
        ];

        yield [
            '<?php use /* foo */Foo\Bar;',
            '<?php use  /* foo */Foo\Bar;',
        ];

        yield [
            '<?php use const Foo\BAR;',
            '<?php use  const Foo\BAR;',
        ];

        yield [
            '<?php use const Foo\BAR;',
            '<?php use

const Foo\BAR;',
        ];

        yield [
            '<?php use /* foo */const Foo\BAR;',
            '<?php use/* foo */const Foo\BAR;',
        ];

        yield [
            '<?php use /* foo */const Foo\BAR;',
            '<?php use/* foo */const Foo\BAR;',
        ];

        yield [
            '<?php use function Foo\bar;',
            '<?php use  function Foo\bar;',
        ];

        yield [
            '<?php use function Foo\bar;',
            '<?php use

function Foo\bar;',
        ];

        yield [
            '<?php use /* foo */function Foo\bar;',
            '<?php use/* foo */function Foo\bar;',
        ];

        yield [
            '<?php use /* foo */function Foo\bar;',
            '<?php use/* foo */function Foo\bar;',
        ];
    }

    /**
     * @dataProvider provideFixWithUseLambdaCases
     *
     * @param array<string, mixed> $configuration
     */
    public function testFixWithUseLambda(string $expected, ?string $input = null, ?array $configuration = null): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithUseLambdaCases(): iterable
    {
        yield [
            '<?php $foo = function () use($bar) {};',
            '<?php $foo = function ()use($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => ['use_lambda'],
                'constructs_followed_by_a_single_space' => [],
            ],
        ];

        yield [
            '<?php $foo = function ()use ($bar) {};',
            '<?php $foo = function ()use($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['use_lambda'],
            ],
        ];

        yield [
            '<?php $foo = function () use ($bar) {};',
            '<?php $foo = function ()use($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => ['use_lambda'],
                'constructs_followed_by_a_single_space' => ['use_lambda'],
            ],
        ];

        yield [
            '<?php $foo = function () use ($bar) {};',
            '<?php $foo = function () use  ($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['use_lambda'],
            ],
        ];

        yield [
            '<?php $foo = function () use ($bar) {};',
            '<?php $foo = function () use

($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['use_lambda'],
            ],
        ];

        yield [
            '<?php $foo = function () use /* foo */($bar) {};',
            '<?php $foo = function () use/* foo */($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['use_lambda'],
            ],
        ];

        yield [
            '<?php $foo = function () use /* foo */($bar) {};',
            '<?php $foo = function () use  /* foo */($bar) {};',
            [
                'constructs_preceded_by_a_single_space' => [],
                'constructs_followed_by_a_single_space' => ['use_lambda'],
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithUseTraitCases
     */
    public function testFixWithUseTrait(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'use_trait',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithUseTraitCases(): iterable
    {
        yield [
            '<?php class Foo { use Bar; }',
            '<?php class Foo { use  Bar; }',
        ];

        yield [
            '<?php class Foo { use Bar; }',
            '<?php class Foo { use

Bar; }',
        ];

        yield [
            '<?php class Foo { use /* foo */Bar; }',
            '<?php class Foo { use/* foo */Bar; }',
        ];

        yield [
            '<?php class Foo { use /* foo */Bar; }',
            '<?php class Foo { use  /* foo */Bar; }',
        ];
    }

    /**
     * @dataProvider provideFixWithVarCases
     */
    public function testFixWithVar(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'var',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithVarCases(): iterable
    {
        yield [
            '<?php class Foo { var $bar; }',
            '<?php class Foo { var$bar; }',
        ];

        yield [
            '<?php class Foo { var $bar; }',
            '<?php class Foo { var  $bar; }',
        ];

        yield [
            '<?php class Foo { var $bar; }',
            '<?php class Foo { var

$bar; }',
        ];

        yield [
            '<?php class Foo { var /* foo */$bar; }',
            '<?php class Foo { var/* foo */$bar; }',
        ];

        yield [
            '<?php class Foo { var /* foo */$bar; }',
            '<?php class Foo { var  /* foo */$bar; }',
        ];
    }

    /**
     * @dataProvider provideFixWithWhileCases
     */
    public function testFixWithWhile(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'while',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithWhileCases(): iterable
    {
        yield [
            '<?php do {} while (true);',
            '<?php do {} while(true);',
        ];

        yield [
            '<?php do {} while (true);',
            '<?php do {} while  (true);',
        ];

        yield [
            '<?php do {} while (true);',
            '<?php do {} while

(true);',
        ];

        yield [
            '<?php do {} while /* foo */(true);',
            '<?php do {} while/* foo */(true);',
        ];

        yield [
            '<?php do {} while /* foo */(true);',
            '<?php do {} while  /* foo */(true);',
        ];
    }

    /**
     * @dataProvider provideFixWithYieldCases
     */
    public function testFixWithYield(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'yield',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithYieldCases(): iterable
    {
        yield [
            '<?php function foo() { yield $foo; }',
            '<?php function foo() { yield$foo; }',
        ];

        yield [
            '<?php function foo() { yield "Foo"; }',
            '<?php function foo() { yield  "Foo"; }',
        ];

        yield [
            '<?php function foo() { yield "Foo"; }',
            '<?php function foo() { yield

"Foo"; }',
        ];

        yield [
            '<?php function foo() { yield /* foo */"Foo"; }',
            '<?php function foo() { yield/* foo */"Foo"; }',
        ];
    }

    /**
     * @dataProvider provideFixWithYieldFromCases
     *
     * @param array<string, mixed> $configuration
     */
    public function testFixWithYieldFrom(string $expected, ?string $input = null, ?array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithYieldFromCases(): iterable
    {
        $configFollowed = [
            'constructs_contain_a_single_space' => [
            ],
            'constructs_followed_by_a_single_space' => [
                'yield_from',
            ],
        ];
        $configContain = [
            'constructs_contain_a_single_space' => [
                'yield_from',
            ],
            'constructs_followed_by_a_single_space' => [
            ],
        ];
        $configAll = [
            'constructs_contain_a_single_space' => [
                'yield_from',
            ],
            'constructs_followed_by_a_single_space' => [
                'yield_from',
            ],
        ];

        yield [
            '<?php function foo() { yield from $foo; }',
            '<?php function foo() { yield from$foo; }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield from baz(); }',
            '<?php function foo() { yield from  baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yIeLd fRoM baz(); }',
            '<?php function foo() { yIeLd fRoM  baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield from baz(); }',
            '<?php function foo() { yield from

baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield from /* foo */baz(); }',
            '<?php function foo() { yield from/* foo */baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield from /* foo */baz(); }',
            '<?php function foo() { yield from  /* foo */baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield from /* foo */baz(); }',
            '<?php function foo() { yield from

/* foo */baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield  from baz(); }',
            '<?php function foo() { yield  from  baz(); }',
            $configFollowed,
        ];

        yield [
            '<?php function foo() { yield from  baz(); }',
            '<?php function foo() { yield  from  baz(); }',
            $configContain,
        ];

        yield [
            '<?php function foo() { yield from baz(); }',
            '<?php function foo() { yield  from  baz(); }',
            $configAll,
        ];

        yield [
            '<?php function foo() { yIeLd fRoM baz(); }',
            '<?php function foo() { yIeLd  fRoM  baz(); }',
            $configAll,
        ];

        yield [
            '<?php function foo() { yield from baz(); }',
            '<?php function foo() { yield

from baz(); }',
            $configContain,
        ];

        yield [
            '<?php function foo() { yield from baz(); }',
            '<?php function foo() { yield

from

baz(); }',
            $configAll,
        ];
    }

    /**
     * @dataProvider provideFixWithPhpOpenCases
     */
    public function testFixWithPhpOpen(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'php_open',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithPhpOpenCases(): iterable
    {
        yield [
            '<?php echo 1;',
            '<?php    echo 1;',
        ];

        yield [
            "<?php\necho 1;",
        ];

        yield [
            "<?php\n   echo 1;",
        ];

        yield [
            '<?php ',
        ];

        yield [
            "<?php\n",
        ];

        yield [
            "<?php \necho 1;",
        ];

        yield [
            "<?php    \n\necho 1;",
        ];
    }

    /**
     * @dataProvider provideCommentsCases
     */
    public function testComments(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'comment',
                'php_doc',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideCommentsCases(): iterable
    {
        yield [
            '<?php
$a /* 1 */ = /**/ 1;
$a /** 1 */ = /** 2 */ 1;

$a = 3; # 3
$a = 4; /** 4 */
echo 1;
',
            '<?php
$a /* 1 */= /**/1;
$a /** 1 */= /** 2 */1;

$a = 3; # 3
$a = 4; /** 4 */
echo 1;
',
        ];

        yield 'exceptions' => [
            '<?php
new Dummy(/* a */);
new Dummy(/** b */);
foo(/* c */);
foo($a /* d */, $b);
$arr = [/* empty */];
',
        ];

        yield 'before_destructuring_square_brace_close' => [
            '<?php
foreach ($fields as [$field/** @var string*/]) {
}
',
        ];
    }

    /**
     * @dataProvider provideWithNamespaceCases
     */
    public function testWithNamespace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'namespace',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideWithNamespaceCases(): iterable
    {
        yield 'simple' => [
            '<?php
namespace Foo;
namespace Bar;',
            '<?php
namespace    Foo;
namespace    Bar;',
        ];

        yield 'simple with newlines' => [
            '<?php
namespace Foo;
namespace Bar;',
            '<?php
namespace
Foo;
namespace
Bar;',
        ];

        yield 'braces' => [
            '<?php
namespace Foo {}
namespace Bar {}',
            '<?php
namespace    Foo {}
namespace    Bar {}',
        ];

        yield 'braces with newlines' => [
            '<?php
namespace Foo {}
namespace Bar {}',
            '<?php
namespace
Foo {}
namespace
Bar {}',
        ];

        yield 'with // comment' => [
            '<?php
namespace // comment
Foo;',
        ];

        yield 'with /* comment */' => [
            '<?php
namespace /* comment */ Foo;',
            '<?php
namespace/* comment */ Foo;',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'match 1' => [
            '<?php echo match ($x) {
    1, 2 => "Same for 1 and 2",
};',
            '<?php      echo              match     ($x) {
    1, 2 => "Same for 1 and 2",
};',
        ];

        yield 'match 2' => [
            '<?php echo match ($x) {
    1, 2 => "Same for 1 and 2",
};',
            '<?php echo match($x) {
    1, 2 => "Same for 1 and 2",
};',
        ];

        yield 'constructor property promotion' => [
            '<?php
class Point {
    public function __construct(
        public float $x = 0.0,
        protected float $y = 0.0,
        private float $z = 0.0,
    ) {}
}
',
            "<?php
class Point {
    public function __construct(
        public       float \$x = 0.0,
        protected\tfloat \$y = 0.0,
        private\nfloat \$z = 0.0,
    ) {}
}
",
        ];

        yield 'attribute' => [
            '<?php class Foo {
    #[Required] // foo
    public $bar1;

    #[Required]
    public $bar2;
}',
            '<?php class Foo {
    #[Required]// foo
    public $bar1;

    #[Required]
    public $bar2;
}',
        ];

        yield 'named argument' => [
            '<?php $foo(test: 1);',
            '<?php $foo(test:    1);',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'readonly' => [
            '<?php
final class Foo
{
    public readonly string $prop;

    public function __construct(
        public readonly float $x = 0.0,
    ) {}
}
            ',
            '<?php
final class Foo
{
    public readonly  string $prop;

    public function __construct(
        public    readonly   float $x = 0.0,
    ) {}
}
            ',
        ];

        yield 'enum' => [
            '<?php
enum Suit {
    case Hearts;
}
',
            '<?php
enum     Suit {
    case Hearts;
}
',
        ];

        yield 'enum full caps' => [
            '<?php
ENUM Suit {
    case Hearts;
}
',
            '<?php
ENUM     Suit {
    case     Hearts;
}
',
        ];

        yield [
            '<?php class Foo
{
    final public const X = "foo";
}',
            '<?php class Foo
{
    final   public   const    X = "foo";
}',
        ];

        yield [
            '<?php
class Test {
    public function __construct(
        public $prop = new Foo,
    ) {}
}
',
            '<?php
class    Test {
    public     function    __construct(
        public    $prop = new     Foo,
    ) {}
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithSwitchCases
     */
    public function testFixWithSwitch(string $expected, string $input): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'switch',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithSwitchCases(): iterable
    {
        yield [
            '<?php
                switch ($a){ case 1: echo 123; }
                switch ($b){ case 1: echo 123; }
            ',
            '<?php
                switch($a){ case 1: echo 123; }
                switch  ($b){ case 1: echo 123; }
            ',
        ];
    }

    /**
     * @dataProvider provideTypeColonCases
     */
    public function testTypeColon(string $expected, string $input): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'type_colon',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideTypeColonCases(): iterable
    {
        yield [
            '<?php function foo(): array { return []; }',
            "<?php function foo():\narray { return []; }",
        ];

        yield [
            '<?php interface F { public function foo(): array; }',
            "<?php interface F { public function foo():\tarray; }",
        ];

        yield [
            '<?php $a=1; $f = function () use($a): array {};',
            '<?php $a=1; $f = function () use($a):array {};',
        ];

        yield [
            '<?php fn()        : array => [];',
            '<?php fn()        :      array => [];',
        ];

        yield [
            '<?php $a=1; $f = fn (): array => [];',
            '<?php $a=1; $f = fn ():      array => [];',
        ];
    }

    /**
     * @dataProvider provideEnumTypeColonCases
     *
     * @requires PHP 8.1
     */
    public function testEnumTypeColon(string $expected, string $input): void
    {
        $this->fixer->configure([
            'constructs_followed_by_a_single_space' => [
                'type_colon',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideEnumTypeColonCases(): iterable
    {
        yield [
            '<?php enum Foo: int {}',
            "<?php enum Foo:\nint {}",
        ];

        yield [
            '<?php enum Foo: string {}',
            '<?php enum Foo:string {}',
        ];
    }
}
