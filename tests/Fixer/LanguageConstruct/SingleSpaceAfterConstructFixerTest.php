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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixer
 */
final class SingleSpaceAfterConstructFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideInvalidConstructCases
     *
     * @param mixed $construct
     */
    public function testConfigureRejectsInvalidControlStatement($construct)
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'constructs' => [
                $construct,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function provideInvalidConstructCases()
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [true],
            'int' => [0],
            'float' => [3.14],
            'array' => [[]],
            'object' => [new \stdClass()],
            'unknown' => ['foo'],
        ];
    }

    /**
     * @dataProvider provideFixWithAbstractCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithAbstract($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'abstract',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAbstractCases()
    {
        return [
            [
                '<?php abstract class Foo {}; if($a){}',
                '<?php abstract  class Foo {}; if($a){}',
            ],
            [
                '<?php abstract class Foo {};',
                '<?php abstract

class Foo {};',
            ],
            [
                '<?php abstract /* foo */class Foo {};',
                '<?php abstract/* foo */class Foo {};',
            ],
            [
                '<?php abstract /* foo */class Foo {};',
                '<?php abstract  /* foo */class Foo {};',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithBreakCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithBreak($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'break',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithBreakCases()
    {
        return [
            [
                '<?php while (true) { break; }',
            ],
            [
                '<?php while (true) { break /* foo */; }',
                '<?php while (true) { break/* foo */; }',
            ],
            [
                '<?php while (true) { break /* foo */; }',
                '<?php while (true) { break  /* foo */; }',
            ],
            [
                '<?php while (true) { break 1; }',
                '<?php while (true) { break  1; }',
            ],
            [
                '<?php while (true) { break 1; }',
                '<?php while (true) { break

1; }',
            ],
            [
                '<?php while (true) { break /* foo */1; }',
                '<?php while (true) { break/* foo */1; }',
            ],
            [
                '<?php while (true) { break /* foo */1; }',
                '<?php while (true) { break  /* foo */1; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithAsCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithAs($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'as',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAsCases()
    {
        return [
            [
                '<?php foreach ($foo as $bar) {}',
                '<?php foreach ($foo as$bar) {}',
            ],
            [
                '<?php foreach ($foo as $bar) {}',
                '<?php foreach ($foo as  $bar) {}',
            ],
            [
                '<?php foreach ($foo as $bar) {}',
                '<?php foreach ($foo as

$bar) {}',
            ],
            [
                '<?php foreach ($foo as /* foo */$bar) {}',
                '<?php foreach ($foo as/* foo */$bar) {}',
            ],
            [
                '<?php foreach ($foo as /* foo */$bar) {}',
                '<?php foreach ($foo as  /* foo */$bar) {}',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCaseCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithCase($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'case',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCaseCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCatchCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithCatch($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'catch',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCatchCases()
    {
        return [
            [
                '<?php try {} catch (\Exception $exception) {}',
                '<?php try {} catch(\Exception $exception) {}',
            ],
            [
                '<?php try {} catch (\Exception $exception) {}',
                '<?php try {} catch  (\Exception $exception) {}',
            ],
            [
                '<?php try {} catch (\Exception $exception) {}',
                '<?php try {} catch

(\Exception $exception) {}',
            ],
            [
                '<?php try {} catch /* foo */(Exception $exception) {}',
                '<?php try {} catch/* foo */(Exception $exception) {}',
            ],
            [
                '<?php try {} catch /* foo */(Exception $exception) {}',
                '<?php try {} catch  /* foo */(Exception $exception) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithClassCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithClass($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'class',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithClassCases()
    {
        return [
            [
                '<?php class Foo {}',
                '<?php class  Foo {}',
            ],
            [
                '<?php class Foo {}',
                '<?php class

Foo {}',
            ],
            [
                '<?php class /* foo */Foo {}',
                '<?php class  /* foo */Foo {}',
            ],
            [
                '<?php class /* foo */Foo {}',
                '<?php class/* foo */Foo {}',
            ],
            [
                '<?php $foo = stdClass::class;',
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     *
     * @dataProvider provideFixWithClassPhp70Cases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithClassPhp70($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFixWithClassPhp70Cases()
    {
        return [
            [
                '<?php $foo = new class {};',
                '<?php $foo = new class  {};',
                ['constructs' => ['class']],
            ],
            [
                '<?php $foo = new class {};',
                '<?php $foo = new class{};',
                ['constructs' => ['class']],
            ],
            [
                '<?php $foo = new class /* foo */{};',
                '<?php $foo = new class/* foo */{};',
                ['constructs' => ['class']],
            ],
            [
                '<?php $foo = new class /* foo */{};',
                '<?php $foo = new class  /* foo */{};',
                ['constructs' => ['class']],
            ],
            [
                '<?php $foo = new class(){};',
                null,
                ['constructs' => ['class']],
            ],
            [
                '<?php return
                    $a ? new class(){ public function foo() { echo 1; }}
                    : 1
                ;',
                null,
                ['constructs' => ['return']],
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithContinueCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithContinue($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'continue',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithContinueCases()
    {
        return [
            [
                '<?php while (true) { continue; }',
            ],
            [
                '<?php while (true) { continue /* foo */; }',
                '<?php while (true) { continue/* foo */; }',
            ],
            [
                '<?php while (true) { continue /* foo */; }',
                '<?php while (true) { continue  /* foo */; }',
            ],
            [
                '<?php while (true) { continue 1; }',
                '<?php while (true) { continue  1; }',
            ],
            [
                '<?php while (true) { continue 1; }',
                '<?php while (true) { continue

1; }',
            ],
            [
                '<?php while (true) { continue /* foo*/ 1; }',
                '<?php while (true) { continue  /* foo*/ 1; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithConstCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConst($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'const',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithConstCases()
    {
        return [
            [
                '<?php class Foo { const FOO = 9000; }',
                '<?php class Foo { const  FOO = 9000; }',
            ],
            [
                '<?php class Foo { const FOO = 9000; }',
                '<?php class Foo { const

FOO = 9000; }',
            ],
            [
                '<?php class Foo { const /* foo */FOO = 9000; }',
                '<?php class Foo { const/* foo */FOO = 9000; }',
            ],
            [
                '<?php class Foo { const /* foo */FOO = 9000; }',
                '<?php class Foo { const  /* foo */FOO = 9000; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithConstImportCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConstImport($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'const_import',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithConstImportCases()
    {
        return [
            [
                '<?php use const FOO\BAR;',
                '<?php use const  FOO\BAR;',
            ],
            [
                '<?php use const FOO\BAR;',
                '<?php use const

FOO\BAR;',
            ],
            [
                '<?php use const /* foo */FOO\BAR;',
                '<?php use const/* foo */FOO\BAR;',
            ],
            [
                '<?php use const /* foo */FOO\BAR;',
                '<?php use const  /* foo */FOO\BAR;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCloneCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithClone($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'clone',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithCloneCases()
    {
        return [
            [
                '<?php clone $foo;',
                '<?php clone$foo;',
            ],
            [
                '<?php clone $foo;',
                '<?php clone  $foo;',
            ],
            [
                '<?php clone $foo;',
                '<?php clone

$foo;',
            ],
            [
                '<?php clone /* foo */$foo;',
                '<?php clone/* foo */$foo;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithDoCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDo($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'do',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithDoCases()
    {
        return [
            [
                '<?php do {} while (true);',
                '<?php do{} while (true);',
            ],
            [
                '<?php do {} while (true);',
                '<?php do  {} while (true);',
            ],
            [
                '<?php do {} while (true);',
                '<?php do

{} while (true);',
            ],
            [
                '<?php do /* foo*/{} while (true);',
                '<?php do/* foo*/{} while (true);',
            ],
            [
                '<?php do /* foo*/{} while (true);',
                '<?php do  /* foo*/{} while (true);',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithEchoCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithEcho($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'echo',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithEchoCases()
    {
        return [
            [
                '<?php echo $foo;',
                '<?php echo$foo;',
            ],
            [
                '<?php echo 9000;',
                '<?php echo  9000;',
            ],
            [
                '<?php echo 9000;',
                '<?php echo

9000;',
            ],
            [
                '<?php echo /* foo */9000;',
                '<?php echo/* foo */9000;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithElseCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithElse($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'else',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithElseCases()
    {
        return [
            [
                '<?php if (true) {} else {}',
                '<?php if (true) {} else{}',
            ],
            [
                '<?php if (true) {} else {}',
                '<?php if (true) {} else  {}',
            ],
            [
                '<?php if (true) {} else {}',
                '<?php if (true) {} else

{}',
            ],
            [
                '<?php if (true) {} else /* foo */{}',
                '<?php if (true) {} else/* foo */{}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithElseIfCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithElseIf($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'elseif',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithElseIfCases()
    {
        return [
            [
                '<?php if (true) {} elseif (false) {}',
                '<?php if (true) {} elseif(false) {}',
            ],
            [
                '<?php if (true) {} elseif (false) {}',
                '<?php if (true) {} elseif  (false) {}',
            ],
            [
                '<?php if (true) {} elseif (false) {}',
                '<?php if (true) {} elseif

(false) {}',
            ],
            [
                '<?php if (true) {} elseif /* foo */(false) {}',
                '<?php if (true) {} elseif/* foo */(false) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithExtendsCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithExtends($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'extends',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithExtendsCases()
    {
        return [
            [
                '<?php class Foo extends \InvalidArgumentException {}',
                '<?php class Foo extends  \InvalidArgumentException {}',
            ],
            [
                '<?php class Foo extends \InvalidArgumentException {}',
                '<?php class Foo extends

\InvalidArgumentException {}',
            ],
            [
                '<?php class Foo extends /* foo */\InvalidArgumentException {}',
                '<?php class Foo extends/* foo */\InvalidArgumentException {}',
            ],
            [
                '<?php class Foo extends /* foo */\InvalidArgumentException {}',
                '<?php class Foo extends  /* foo */\InvalidArgumentException {}',
            ],
            [
                '<?php interface Foo extends Bar1 {}',
                '<?php interface Foo extends  Bar1 {}',
            ],
            [
                '<?php interface Foo extends Bar2 {}',
                '<?php interface Foo extends

Bar2 {}',
            ],
            [
                '<?php interface Foo extends /* foo */Bar3 {}',
                '<?php interface Foo extends/* foo */Bar3 {}',
            ],
            [
                '<?php interface Foo extends /* foo */Bar4 {}',
                '<?php interface Foo extends  /* foo */Bar4 {}',
            ],
            [
                '<?php interface Foo extends Bar5, Baz, Qux {}',
                '<?php interface Foo extends  Bar5, Baz, Qux {}',
            ],
            [
                '<?php interface Foo extends Bar6, Baz, Qux {}',
                '<?php interface Foo extends

Bar6, Baz, Qux {}',
            ],
            [
                '<?php interface Foo extends /* foo */Bar7, Baz, Qux {}',
                '<?php interface Foo extends/* foo */Bar7, Baz, Qux {}',
            ],
            [
                '<?php interface Foo extends /* foo */Bar8, Baz, Qux {}',
                '<?php interface Foo extends  /* foo */Bar8, Baz, Qux {}',
            ],
            [
                '<?php interface Foo extends
    Bar9,
    Baz,
    Qux
{}',
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     *
     * @dataProvider provideFixWithExtendsPhp70Cases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithExtendsPhp70($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'extends',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithExtendsPhp70Cases()
    {
        return [
            [
                '<?php $foo = new class extends \InvalidArgumentException {};',
                '<?php $foo = new class extends  \InvalidArgumentException {};',
            ],
            [
                '<?php $foo = new class extends \InvalidArgumentException {};',
                '<?php $foo = new class extends

\InvalidArgumentException {};',
            ],
            [
                '<?php $foo = new class extends /* foo */\InvalidArgumentException {};',
                '<?php $foo = new class extends/* foo */\InvalidArgumentException {};',
            ],
            [
                '<?php $foo = new class extends /* foo */\InvalidArgumentException {};',
                '<?php $foo = new class extends  /* foo */\InvalidArgumentException {};',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithFinalCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithFinal($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'final',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithFinalCases()
    {
        return [
            [
                '<?php final class Foo {}',
                '<?php final  class Foo {}',
            ],
            [
                '<?php final class Foo {}',
                '<?php final

class Foo {}',
            ],
            [
                '<?php final /* foo */class Foo {}',
                '<?php final/* foo */class Foo {}',
            ],
            [
                '<?php final /* foo */class Foo {}',
                '<?php final  /* foo */class Foo {}',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithFinallyCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithFinally($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'finally',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithFinallyCases()
    {
        return [
            [
                '<?php try {} finally {}',
                '<?php try {} finally{}',
            ],
            [
                '<?php try {} finally {}',
                '<?php try {} finally  {}',
            ],
            [
                '<?php try {} finally {}',
                '<?php try {} finally

{}',
            ],
            [
                '<?php try {} finally /* foo */{}',
                '<?php try {} finally/* foo */{}',
            ],
            [
                '<?php try {} finally /* foo */{}',
                '<?php try {} finally  /* foo */{}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithForCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithFor($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'for',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithForCases()
    {
        return [
            [
                '<?php for ($i = 0; $i < 3; ++$i) {}',
                '<?php for($i = 0; $i < 3; ++$i) {}',
            ],
            [
                '<?php for ($i = 0; $i < 3; ++$i) {}',
                '<?php for  ($i = 0; $i < 3; ++$i) {}',
            ],
            [
                '<?php for ($i = 0; $i < 3; ++$i) {}',
                '<?php for

($i = 0; $i < 3; ++$i) {}',
            ],
            [
                '<?php for /* foo */($i = 0; $i < 3; ++$i) {}',
                '<?php for/* foo */($i = 0; $i < 3; ++$i) {}',
            ],
            [
                '<?php for /* foo */($i = 0; $i < 3; ++$i) {}',
                '<?php for  /* foo */($i = 0; $i < 3; ++$i) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithForeachCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithForeach($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'foreach',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithForeachCases()
    {
        return [
            [
                '<?php foreach ($foo as $bar) {}',
                '<?php foreach($foo as $bar) {}',
            ],
            [
                '<?php foreach ($foo as $bar) {}',
                '<?php foreach  ($foo as $bar) {}',
            ],
            [
                '<?php foreach ($foo as $bar) {}',
                '<?php foreach

($foo as $bar) {}',
            ],
            [
                '<?php foreach /* foo */($foo as $bar) {}',
                '<?php foreach/* foo */($foo as $bar) {}',
            ],
            [
                '<?php foreach /* foo */($foo as $bar) {}',
                '<?php foreach  /* foo */($foo as $bar) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithFunctionCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithFunction($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'function',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithFunctionCases()
    {
        return [
            [
                '<?php function foo() {}',
                '<?php function  foo() {}',
            ],
            [
                '<?php function foo() {}',
                '<?php function

foo() {}',
            ],
            [
                '<?php function /* foo */foo() {}',
                '<?php function/* foo */foo() {}',
            ],
            [
                '<?php function /* foo */foo() {}',
                '<?php function  /* foo */foo() {}',
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithFunctionImportCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithFunctionImport($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'function_import',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithFunctionImportCases()
    {
        return [
            [
                '<?php use function Foo\bar;',
                '<?php use function  Foo\bar;',
            ],
            [
                '<?php use function Foo\bar;',
                '<?php use function

Foo\bar;',
            ],
            [
                '<?php use function /* foo */Foo\bar;',
                '<?php use function/* foo */Foo\bar;',
            ],
            [
                '<?php use function /* foo */Foo\bar;',
                '<?php use function  /* foo */Foo\bar;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithGlobalCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithGlobal($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'global',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithGlobalCases()
    {
        return [
            [
                '<?php function foo() { global $bar; }',
                '<?php function foo() { global$bar; }',
            ],
            [
                '<?php function foo() { global $bar; }',
                '<?php function foo() { global  $bar; }',
            ],
            [
                '<?php function foo() { global $bar; }',
                '<?php function foo() { global

$bar; }',
            ],
            [
                '<?php function foo() { global /* foo */$bar; }',
                '<?php function foo() { global/* foo */$bar; }',
            ],
            [
                '<?php function foo() { global /* foo */$bar; }',
                '<?php function foo() { global  /* foo */$bar; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithGotoCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithGoto($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'goto',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithGotoCases()
    {
        return [
            [
                '<?php goto foo; foo: echo "Bar";',
                '<?php goto  foo; foo: echo "Bar";',
            ],
            [
                '<?php goto foo; foo: echo "Bar";',
                '<?php goto

foo; foo: echo "Bar";',
            ],
            [
                '<?php goto /* foo */foo; foo: echo "Bar";',
                '<?php goto/* foo */foo; foo: echo "Bar";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithIfCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithIf($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'if',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithIfCases()
    {
        return [
            [
                '<?php if ($foo === $bar) {}',
                '<?php if($foo === $bar) {}',
            ],
            [
                '<?php if ($foo === $bar) {}',
                '<?php if  ($foo === $bar) {}',
            ],
            [
                '<?php if ($foo === $bar) {}',
                '<?php if

($foo === $bar) {}',
            ],
            [
                '<?php if /* foo */($foo === $bar) {}',
                '<?php if/* foo */($foo === $bar) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithImplementsCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithImplements($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'implements',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithImplementsCases()
    {
        return [
            [
                '<?php class Foo implements \Countable {}',
                '<?php class Foo implements  \Countable {}',
            ],
            [
                '<?php class Foo implements \Countable {}',
                '<?php class Foo implements

\Countable {}',
            ],
            [
                '<?php class Foo implements /* foo */\Countable {}',
                '<?php class Foo implements/* foo */\Countable {}',
            ],
            [
                '<?php class Foo implements /* foo */\Countable {}',
                '<?php class Foo implements  /* foo */\Countable {}',
            ],
            [
                '<?php class Foo implements
                    \Countable,
                    Bar,
                    Baz
                {}',
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     *
     * @dataProvider provideFixWithImplementsPhp70Cases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithImplementsPhp70($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'implements',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithImplementsPhp70Cases()
    {
        return [
            [
                '<?php $foo = new class implements \Countable {};',
                '<?php $foo = new class implements  \Countable {};',
            ],
            [
                '<?php $foo = new class implements \Countable {};',
                '<?php $foo = new class implements

\Countable {};',
            ],
            [
                '<?php $foo = new class implements /* foo */\Countable {};',
                '<?php $foo = new class implements/* foo */\Countable {};',
            ],
            [
                '<?php $foo = new class implements /* foo */\Countable {};',
                '<?php $foo = new class implements  /* foo */\Countable {};',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithInclude($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'include',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithIncludeCases()
    {
        return [
            [
                '<?php include "vendor/autoload.php";',
                '<?php include"vendor/autoload.php";',
            ],
            [
                '<?php include "vendor/autoload.php";',
                '<?php include  "vendor/autoload.php";',
            ],
            [
                '<?php include "vendor/autoload.php";',
                '<?php include

"vendor/autoload.php";',
            ],
            [
                '<?php include /* foo */"vendor/autoload.php";',
                '<?php include/* foo */"vendor/autoload.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeOnceCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithIncludeOnce($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'include_once',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithIncludeOnceCases()
    {
        return [
            [
                '<?php include_once "vendor/autoload.php";',
                '<?php include_once"vendor/autoload.php";',
            ],
            [
                '<?php include_once "vendor/autoload.php";',
                '<?php include_once  "vendor/autoload.php";',
            ],
            [
                '<?php include_once "vendor/autoload.php";',
                '<?php include_once

"vendor/autoload.php";',
            ],
            [
                '<?php include_once /* foo */"vendor/autoload.php";',
                '<?php include_once/* foo */"vendor/autoload.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithInstanceofCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithInstanceof($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'instanceof',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithInstanceofCases()
    {
        return [
            [
                '<?php $foo instanceof \stdClass;',
                '<?php $foo instanceof  \stdClass;',
            ],
            [
                '<?php $foo instanceof \stdClass;',
                '<?php $foo instanceof

\stdClass;',
            ],
            [
                '<?php $foo instanceof /* foo */\stdClass;',
                '<?php $foo instanceof/* foo */\stdClass;',
            ],
            [
                '<?php $foo instanceof /* foo */\stdClass;',
                '<?php $foo instanceof  /* foo */\stdClass;',
            ],
            [
                '<?php $foo instanceof $bar;',
                '<?php $foo instanceof$bar;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithInsteadofCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithInsteadof($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'insteadof',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithInsteadofCases()
    {
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithInterfaceCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithInterface($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'interface',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithInterfaceCases()
    {
        return [
            [
                '<?php interface Foo {}',
                '<?php interface  Foo {}',
            ],
            [
                '<?php interface Foo {}',
                '<?php interface

Foo {}',
            ],
            [
                '<?php interface /* foo */Foo {}',
                '<?php interface  /* foo */Foo {}',
            ],
            [
                '<?php interface /* foo */Foo {}',
                '<?php interface/* foo */Foo {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithNewCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithNew($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'new',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithNewCases()
    {
        return [
            [
                '<?php new $foo();',
                '<?php new$foo();',
            ],
            [
                '<?php new Bar();',
                '<?php new  Bar();',
            ],
            [
                '<?php new Bar();',
                '<?php new

Bar();',
            ],
            [
                '<?php new /* foo */Bar();',
                '<?php new/* foo */Bar();',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithOpenTagWithEchoCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithOpenTagWithEcho($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'open_tag_with_echo',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithOpenTagWithEchoCases()
    {
        return [
            [
                '<?= $foo ?>',
                '<?=$foo ?>',
            ],
            [
                '<?= $foo ?>',
                '<?=  $foo ?>',
            ],
            [
                '<?= $foo ?>',
                '<?=

$foo ?>',
            ],
            [
                '<?= /* foo */$foo ?>',
                '<?=/* foo */$foo ?>',
            ],
            [
                '<?= /* foo */$foo ?>',
                '<?=  /* foo */$foo ?>',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPrintCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithPrint($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'print',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPrintCases()
    {
        return [
            [
                '<?php print $foo;',
                '<?php print$foo;',
            ],
            [
                '<?php print 9000;',
                '<?php print  9000;',
            ],
            [
                '<?php print 9000;',
                '<?php print

9000;',
            ],
            [
                '<?php print /* foo */9000;',
                '<?php print/* foo */9000;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPrivateCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithPrivate($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'private',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPrivateCases()
    {
        return [
            [
                '<?php class Foo { private $bar; }',
                '<?php class Foo { private$bar; }',
            ],
            [
                '<?php class Foo { private $bar; }',
                '<?php class Foo { private  $bar; }',
            ],
            [
                '<?php class Foo { private $bar; }',
                '<?php class Foo { private

$bar; }',
            ],
            [
                '<?php class Foo { private /* foo */$bar; }',
                '<?php class Foo { private/* foo */$bar; }',
            ],
            [
                '<?php class Foo { private /* foo */$bar; }',
                '<?php class Foo { private  /* foo */$bar; }',
            ],
            [
                '<?php class Foo { private function bar() {} }',
                '<?php class Foo { private  function bar() {} }',
            ],
            [
                '<?php class Foo { private function bar() {} }',
                '<?php class Foo { private

function bar() {} }',
            ],
            [
                '<?php class Foo { private /* foo */function bar() {} }',
                '<?php class Foo { private/* foo */function bar() {} }',
            ],
            [
                '<?php class Foo { private /* foo */function bar() {} }',
                '<?php class Foo { private  /* foo */function bar() {} }',
            ],
        ];
    }

    /**
     * @requires PHP 7.1
     *
     * @dataProvider provideFixWithPrivatePhp71Cases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithPrivatePhp71($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'private',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPrivatePhp71Cases()
    {
        return [
            [
                '<?php class Foo { private CONST BAR = 9000; }',
                '<?php class Foo { private  CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { private CONST BAR = 9000; }',
                '<?php class Foo { private

CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { private /* foo */CONST BAR = 9000; }',
                '<?php class Foo { private/* foo */CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { private /* foo */CONST BAR = 9000; }',
                '<?php class Foo { private  /* foo */CONST BAR = 9000; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithProtectedCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithProtected($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'protected',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithProtectedCases()
    {
        return [
            [
                '<?php class Foo { protected $bar; }',
                '<?php class Foo { protected$bar; }',
            ],
            [
                '<?php class Foo { protected $bar; }',
                '<?php class Foo { protected  $bar; }',
            ],
            [
                '<?php class Foo { protected $bar; }',
                '<?php class Foo { protected

$bar; }',
            ],
            [
                '<?php class Foo { protected /* foo */$bar; }',
                '<?php class Foo { protected/* foo */$bar; }',
            ],
            [
                '<?php class Foo { protected /* foo */$bar; }',
                '<?php class Foo { protected  /* foo */$bar; }',
            ],
            [
                '<?php class Foo { protected function bar() {} }',
                '<?php class Foo { protected  function bar() {} }',
            ],
            [
                '<?php class Foo { protected function bar() {} }',
                '<?php class Foo { protected

function bar() {} }',
            ],
            [
                '<?php class Foo { protected /* foo */function bar() {} }',
                '<?php class Foo { protected/* foo */function bar() {} }',
            ],
            [
                '<?php class Foo { protected /* foo */function bar() {} }',
                '<?php class Foo { protected  /* foo */function bar() {} }',
            ],
        ];
    }

    /**
     * @requires PHP 7.1
     *
     * @dataProvider provideFixWithProtectedPhp71Cases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithProtectedPhp71($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'protected',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithProtectedPhp71Cases()
    {
        return [
            [
                '<?php class Foo { protected CONST BAR = 9000; }',
                '<?php class Foo { protected  CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { protected CONST BAR = 9000; }',
                '<?php class Foo { protected

CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { protected /* foo */CONST BAR = 9000; }',
                '<?php class Foo { protected/* foo */CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { protected /* foo */CONST BAR = 9000; }',
                '<?php class Foo { protected  /* foo */CONST BAR = 9000; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPublicCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithPublic($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'public',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPublicCases()
    {
        return [
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public$bar; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public  $bar; }',
            ],
            [
                '<?php class Foo { public $bar; }',
                '<?php class Foo { public

$bar; }',
            ],
            [
                '<?php class Foo { public /* foo */$bar; }',
                '<?php class Foo { public/* foo */$bar; }',
            ],
            [
                '<?php class Foo { public /* foo */$bar; }',
                '<?php class Foo { public  /* foo */$bar; }',
            ],
            [
                '<?php class Foo { public function bar() {} }',
                '<?php class Foo { public  function bar() {} }',
            ],
            [
                '<?php class Foo { public function bar() {} }',
                '<?php class Foo { public

function bar() {} }',
            ],
            [
                '<?php class Foo { public /* foo */function bar() {} }',
                '<?php class Foo { public/* foo */function bar() {} }',
            ],
            [
                '<?php class Foo { public /* foo */function bar() {} }',
                '<?php class Foo { public  /* foo */function bar() {} }',
            ],
        ];
    }

    /**
     * @requires PHP 7.1
     *
     * @dataProvider provideFixWithPublicPhp71Cases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithPublicPhp71($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'public',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPublicPhp71Cases()
    {
        return [
            [
                '<?php class Foo { public CONST BAR = 9000; }',
                '<?php class Foo { public  CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { public CONST BAR = 9000; }',
                '<?php class Foo { public

CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { public /* foo */CONST BAR = 9000; }',
                '<?php class Foo { public/* foo */CONST BAR = 9000; }',
            ],
            [
                '<?php class Foo { public /* foo */CONST BAR = 9000; }',
                '<?php class Foo { public  /* foo */CONST BAR = 9000; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithRequireCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithRequire($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'require',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithRequireCases()
    {
        return [
            [
                '<?php require "vendor/autoload.php";',
                '<?php require"vendor/autoload.php";',
            ],
            [
                '<?php require "vendor/autoload.php";',
                '<?php require  "vendor/autoload.php";',
            ],
            [
                '<?php require "vendor/autoload.php";',
                '<?php require

"vendor/autoload.php";',
            ],
            [
                '<?php require /* foo */"vendor/autoload.php";',
                '<?php require/* foo */"vendor/autoload.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithRequireOnceCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithRequireOnce($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'require_once',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithRequireOnceCases()
    {
        return [
            [
                '<?php require_once "vendor/autoload.php";',
                '<?php require_once"vendor/autoload.php";',
            ],
            [
                '<?php require_once "vendor/autoload.php";',
                '<?php require_once  "vendor/autoload.php";',
            ],
            [
                '<?php require_once "vendor/autoload.php";',
                '<?php require_once

"vendor/autoload.php";',
            ],
            [
                '<?php require_once /* foo */"vendor/autoload.php";',
                '<?php require_once/* foo */"vendor/autoload.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithReturnCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithReturn($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'return',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithReturnCases()
    {
        return [
            [
                '<?php return;',
            ],
            [
                '<?php return /* foo */;',
                '<?php return/* foo */;',
            ],
            [
                '<?php return /* foo */;',
                '<?php return  /* foo */;',
            ],
            [
                '<?php return $foo;',
                '<?php return$foo;',
            ],
            [
                '<?php return 9000;',
                '<?php return  9000;',
            ],
            [
                '<?php return 9000;',
                '<?php return

9000;',
            ],
            [
                '<?php return /* */ 9000 + 1 /* foo */       ?>',
                '<?php return





/* */ 9000 + 1 /* foo */       ?>',
            ],
            [
                '<?php return /* foo */9000;',
                '<?php return/* foo */9000;',
            ],
            [
                '<?php return $foo && $bar || $baz;',
                '<?php return

$foo && $bar || $baz;',
            ],
            [
                '<?php

return
    $foo
    && $bar
    || $baz;',
            ],
            [
                '<?php

return
    $foo &&
    $bar ||
    $baz;',
            ],
            [
                '<?php

return
    $foo
    + $bar
    - $baz;',
            ],
            [
                '<?php

return
    $foo +
    $bar -
    $baz;',
            ],
            [
                '<?php

return
    $foo ?
    $bar :
    $baz;',
            ],
            [
                '<?php

return
    $foo
    ? $bar
    : baz;',
            ],
            [
                '<?php

return
    $foo ?:
    $bar;',
            ],
            [
                '<?php

return
    $foo
    ?: $bar;',
            ],
            [
                '<?php

return
    $foo
    ?: $bar?>',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithStaticCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithStatic($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'static',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithStaticCases()
    {
        return [
            [
                '<?php function foo() { static $bar; }',
                '<?php function foo() { static$bar; }',
            ],
            [
                '<?php function foo() { static $bar; }',
                '<?php function foo() { static  $bar; }',
            ],
            [
                '<?php function foo() { static $bar; }',
                '<?php function foo() { static

$bar; }',
            ],
            [
                '<?php function foo() { static /* foo */$bar; }',
                '<?php function foo() { static/* foo */$bar; }',
            ],
            [
                '<?php function foo() { static /* foo */$bar; }',
                '<?php function foo() { static  /* foo */$bar; }',
            ],
            [
                '<?php class Foo { static function bar() {} }',
                '<?php class Foo { static  function bar() {} }',
            ],
            [
                '<?php class Foo { static function bar() {} }',
                '<?php class Foo { static

function bar() {} }',
            ],
            [
                '<?php class Foo { static /* foo */function bar() {} }',
                '<?php class Foo { static/* foo */function bar() {} }',
            ],
            [
                '<?php class Foo { static /* foo */function bar() {} }',
                '<?php class Foo { static  /* foo */function bar() {} }',
            ],
            [
                '<?php class Foo { function bar() { return new static(); } }',
            ],
            [
                '<?php class Foo { function bar() { return static::class; } }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithThrowCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithThrow($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'throw',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithThrowCases()
    {
        return [
            [
                '<?php throw $foo;',
                '<?php throw$foo;',
            ],
            [
                '<?php throw new Exception();',
                '<?php throw  new Exception();',
            ],
            [
                '<?php throw new Exception();',
                '<?php throw

new Exception();',
            ],
            [
                '<?php throw /* foo */new Exception();',
                '<?php throw/* foo */new Exception();',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithTraitCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithTrait($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'trait',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithTraitCases()
    {
        return [
            [
                '<?php trait Foo {}',
                '<?php trait  Foo {}',
            ],
            [
                '<?php trait Foo {}',
                '<?php trait

Foo {}',
            ],
            [
                '<?php trait /* foo */Foo {}',
                '<?php trait  /* foo */Foo {}',
            ],
            [
                '<?php trait /* foo */Foo {}',
                '<?php trait/* foo */Foo {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithTryCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithTry($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'try',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithTryCases()
    {
        return [
            [
                '<?php try {} catch (\Exception $exception) {}',
                '<?php try{} catch (\Exception $exception) {}',
            ],
            [
                '<?php try {} catch (\Exception $exception) {}',
                '<?php try  {} catch (\Exception $exception) {}',
            ],
            [
                '<?php try {} catch (\Exception $exception) {}',
                '<?php try

{} catch (\Exception $exception) {}',
            ],
            [
                '<?php try /* foo */{} catch (\Exception $exception) {}',
                '<?php try/* foo */{} catch (\Exception $exception) {}',
            ],
            [
                '<?php try /* foo */{} catch (\Exception $exception) {}',
                '<?php try  /* foo */{} catch (\Exception $exception) {}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithUseCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithUse($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'use',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithUseCases()
    {
        return [
            [
                '<?php use Foo\Bar;',
                '<?php use  Foo\Bar;',
            ],
            [
                '<?php use Foo\Bar;',
                '<?php use

Foo\Bar;',
            ],
            [
                '<?php use /* foo */Foo\Bar;',
                '<?php use/* foo */Foo\Bar;',
            ],
            [
                '<?php use /* foo */Foo\Bar;',
                '<?php use  /* foo */Foo\Bar;',
            ],
            [
                '<?php use const Foo\BAR;',
                '<?php use  const Foo\BAR;',
            ],
            [
                '<?php use const Foo\BAR;',
                '<?php use

const Foo\BAR;',
            ],
            [
                '<?php use /* foo */const Foo\BAR;',
                '<?php use/* foo */const Foo\BAR;',
            ],
            [
                '<?php use /* foo */const Foo\BAR;',
                '<?php use/* foo */const Foo\BAR;',
            ],
            [
                '<?php use function Foo\bar;',
                '<?php use  function Foo\bar;',
            ],
            [
                '<?php use function Foo\bar;',
                '<?php use

function Foo\bar;',
            ],
            [
                '<?php use /* foo */function Foo\bar;',
                '<?php use/* foo */function Foo\bar;',
            ],
            [
                '<?php use /* foo */function Foo\bar;',
                '<?php use/* foo */function Foo\bar;',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithUseLambdaCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithUseLambda($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'use_lambda',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithUseLambdaCases()
    {
        return [
            [
                '<?php $foo = function () use ($bar) {};',
                '<?php $foo = function () use($bar) {};',
            ],
            [
                '<?php $foo = function () use ($bar) {};',
                '<?php $foo = function () use  ($bar) {};',
            ],
            [
                '<?php $foo = function () use ($bar) {};',
                '<?php $foo = function () use

($bar) {};',
            ],
            [
                '<?php $foo = function () use /* foo */($bar) {};',
                '<?php $foo = function () use/* foo */($bar) {};',
            ],
            [
                '<?php $foo = function () use /* foo */($bar) {};',
                '<?php $foo = function () use  /* foo */($bar) {};',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithUseTraitCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithUseTrait($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'use_trait',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithUseTraitCases()
    {
        return [
            [
                '<?php class Foo { use Bar; }',
                '<?php class Foo { use  Bar; }',
            ],
            [
                '<?php class Foo { use Bar; }',
                '<?php class Foo { use

Bar; }',
            ],
            [
                '<?php class Foo { use /* foo */Bar; }',
                '<?php class Foo { use/* foo */Bar; }',
            ],
            [
                '<?php class Foo { use /* foo */Bar; }',
                '<?php class Foo { use  /* foo */Bar; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithVarCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithVar($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'var',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithVarCases()
    {
        return [
            [
                '<?php class Foo { var $bar; }',
                '<?php class Foo { var$bar; }',
            ],
            [
                '<?php class Foo { var $bar; }',
                '<?php class Foo { var  $bar; }',
            ],
            [
                '<?php class Foo { var $bar; }',
                '<?php class Foo { var

$bar; }',
            ],
            [
                '<?php class Foo { var /* foo */$bar; }',
                '<?php class Foo { var/* foo */$bar; }',
            ],
            [
                '<?php class Foo { var /* foo */$bar; }',
                '<?php class Foo { var  /* foo */$bar; }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithWhileCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithWhile($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'while',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithWhileCases()
    {
        return [
            [
                '<?php do {} while (true);',
                '<?php do {} while(true);',
            ],
            [
                '<?php do {} while (true);',
                '<?php do {} while  (true);',
            ],
            [
                '<?php do {} while (true);',
                '<?php do {} while

(true);',
            ],
            [
                '<?php do {} while /* foo */(true);',
                '<?php do {} while/* foo */(true);',
            ],
            [
                '<?php do {} while /* foo */(true);',
                '<?php do {} while  /* foo */(true);',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithYieldCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithYield($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'yield',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithYieldCases()
    {
        return [
            [
                '<?php function foo() { yield $foo; }',
                '<?php function foo() { yield$foo; }',
            ],
            [
                '<?php function foo() { yield "Foo"; }',
                '<?php function foo() { yield  "Foo"; }',
            ],
            [
                '<?php function foo() { yield "Foo"; }',
                '<?php function foo() { yield

"Foo"; }',
            ],
            [
                '<?php function foo() { yield /* foo */"Foo"; }',
                '<?php function foo() { yield/* foo */"Foo"; }',
            ],
        ];
    }

    /**
     * @requires PHP 7.0
     *
     * @dataProvider provideFixWithYieldFromCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithYieldFrom($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'yield_from',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithYieldFromCases()
    {
        return [
            [
                '<?php function foo() { yield from baz(); }',
                '<?php function foo() { yield  from baz(); }',
            ],
            [
                '<?php function foo() { yield from $foo; }',
                '<?php function foo() { yield from$foo; }',
            ],
            [
                '<?php function foo() { yield from baz(); }',
                '<?php function foo() { yield from  baz(); }',
            ],
            [
                '<?php function foo() { yield from baz(); }',
                '<?php function foo() { yield  from  baz(); }',
            ],
            [
                '<?php function foo() { yIeLd fRoM baz(); }',
                '<?php function foo() { yIeLd  fRoM  baz(); }',
            ],
            [
                '<?php function foo() { yield from baz(); }',
                '<?php function foo() { yield

from baz(); }',
            ],
            [
                '<?php function foo() { yield from baz(); }',
                '<?php function foo() { yield from

baz(); }',
            ],
            [
                '<?php function foo() { yield from baz(); }',
                '<?php function foo() { yield

from

baz(); }',
            ],
            [
                '<?php function foo() { yield from /* foo */baz(); }',
                '<?php function foo() { yield from/* foo */baz(); }',
            ],
            [
                '<?php function foo() { yield from /* foo */baz(); }',
                '<?php function foo() { yield from  /* foo */baz(); }',
            ],
            [
                '<?php function foo() { yield from /* foo */baz(); }',
                '<?php function foo() { yield from

/* foo */baz(); }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithPhpOpenCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithPhpOpen($expected, $input = null)
    {
        $this->fixer->configure([
            'constructs' => [
                'php_open',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithPhpOpenCases()
    {
        return [
            [
                '<?php echo 1;',
                '<?php    echo 1;',
            ],
            [
                "<?php\necho 1;",
            ],
            [
                "<?php\n   echo 1;",
            ],
            [
                '<?php ',
            ],
            [
                "<?php\n",
            ],
        ];
    }

    /**
     * @dataProvider provideCommentsCases
     *
     * @param string $expected
     * @param string $input
     */
    public function testComments($expected, $input)
    {
        $this->fixer->configure([
            'constructs' => [
                'comment',
                'php_doc',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    public function provideCommentsCases()
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
    }

    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider provideFix80Cases
     * @requires PHP 8.0
     */
    public function testFix80($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases()
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
}
