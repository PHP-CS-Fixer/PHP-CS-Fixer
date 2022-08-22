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

namespace PhpCsFixer\Tests\Fixer\Casing;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Casing\ClassReferenceNameCasingFixer
 */
final class ClassReferenceNameCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield [
            '<?php
                $a = new Exception;
                $b = new \Exception;
                $c = new Exception();
                $d = new \Exception();
                $e = "a".Exception::class;
                $f = "a".\Exception::class;
                $g .= "exception";
                echo \Exception::class;
                print(Exception::class);
                // $a = new exception();
                /** $a = new exception(); */
            ',
            '<?php
                $a = new exception;
                $b = new \exception;
                $c = new exception();
                $d = new \exception();
                $e = "a".exception::class;
                $f = "a".\exception::class;
                $g .= "exception";
                echo \exception::class;
                print(exception::class);
                // $a = new exception();
                /** $a = new exception(); */
            ',
        ];

        yield [
            '<?php namespace Foo {
                $a = new exception;
                $b = new \Exception;
            }',
            '<?php namespace Foo {
                $a = new exception;
                $b = new \exception;
            }',
        ];

        yield [
            '<?php namespace Foo;
                $a = new exception;
                $b = new \Exception;
            ',
            '<?php namespace Foo;
                $a = new exception;
                $b = new \EXCEPTION;
            ',
        ];

        yield [
            '<?php
                $a = exception();
                $b = new A\exception;
                $c = new A\B\C\exception;

                $a1 = \exception();
                $b1 = new \A\exception;
                $c1 = new \A\B\C\exception;
            ',
        ];

        yield [
            '<?php class Foo extends Exception {};',
            '<?php class Foo extends exception {};',
        ];

        yield [
            '<?php class exception {}; new foO();',
        ];

        yield [
            '<?php interface exception {};',
        ];

        yield [
            '<?php trait exception {};',
        ];

        yield [
            '<?php function exception() {};',
        ];

        yield [
            '<?php const exception = "abc";',
        ];

        yield [
            '<?php $a = Foo::exception;',
        ];

        yield [
            '<?php $a = $foo->exception;',
        ];

        yield [
            '<?php use Foo as exception;',
        ];

        yield [
            '<?php class Foo { use exception; }',
        ];

        yield [
            '<?php $foo = ["const" => exception];',
        ];

        yield [
            '<?php
namespace {
    $a = new Exception;
    $b = new \Exception;
}

namespace Bar {
    $a = new exception;
    $b = new \Exception;
}

namespace Foo {
    $a = new exception;
    $b = new \Exception;
    $c = new foO();
}',
            '<?php
namespace {
    $a = new exception;
    $b = new \exception;
}

namespace Bar {
    $a = new exception;
    $b = new \exception;
}

namespace Foo {
    $a = new exception;
    $b = new \exception;
    $c = new foO();
}',
        ];

        yield [
            '<?php use Exception as baR;',
            '<?php use exception as baR;',
        ];

        yield [
            '<?php try { foo(); } catch(\LogicException $e) {}',
            '<?php try { foo(); } catch(\logicexception $e) {}',
        ];

        yield [
            '<?php try { foo(); } catch(LogicException $e) {}',
            '<?php try { foo(); } catch(logicexception $e) {}',
        ];

        yield [
            '<?php
                Closure::bind(fn () => null, null, new class {});
                \Closure::bind(fn () => null, null, new class {});

                Foo\Bar::bind(fn () => null, null, new class {});
                \A\B\\Bar::bind(fn () => null, null, new class {});
            ',
            '<?php
                CLOSURE::bind(fn () => null, null, new class {});
                \CLOSURE::bind(fn () => null, null, new class {});

                Foo\Bar::bind(fn () => null, null, new class {});
                \A\B\\Bar::bind(fn () => null, null, new class {});
            ',
        ];

        yield [
            "<?php
declare(strict_types=1);
use Sonata\\Exporter\\Writer\\EXCEPTION;
\$services->set('sonata.exporter.writer.xml', EXCEPTION::class);
",
        ];

        yield [
            '<?php
                const error = 1;
                foo(error);
                $b2 = $a[error];
                $b21 = [1,error];
                $b22 = [error,2];
                $b23 = [1,error,2];
                $b24 = [1,error,2,];
                $b3 = [error];
                $b4 = $a->{error};
            ',
        ];

        yield [
            '<?php
                foo(\error);
                $b2 = $a[\error];
                $b21 = [1,\error];
                $b22 = [\error,2];
                $b23 = [1,\error,2];
                $b24 = [1,\error,2,];
                $b3 = [\error];
                $b4 = $a->{\error};
            ',
        ];

        yield ['<?php echo error ?><?php echo error;'];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php enum exception {}',
        ];

        yield [
            '<?php enum Foo {
                case exception;
            }',
        ];

        yield 'multiple type catch with variable' => [
            '<?php try { foo(); } catch(\InvalidArgumentException|\LogicException $e) {}',
            '<?php try { foo(); } catch(\INVALIDARGUMENTEXCEPTION|\logicexception $e) {}',
        ];

        yield 'multiple type catch without variable 3' => [
            '<?php try { foo(); } catch(\InvalidArgumentException|\LogicException) {}',
            '<?php try { foo(); } catch(\INVALIDARGUMENTEXCEPTION|\logicexception) {}',
        ];
    }
}
