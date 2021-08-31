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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer
 */
final class SingleLineAfterImportsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php
use D;
use E;
use DP;   /**/
use EZ; //
use DAZ;
use EGGGG; /**/
use A\B;

use C\DE;


use E\F;



use G\H;

',
                '<?php
use D;         use E;
use DP;   /**/      use EZ; //
use DAZ;         use EGGGG; /**/
use A\B;

use C\DE;


use E\F;



use G\H;
',
            ],
            [
                '<?php use \Exception;

?>
<?php
$a = new Exception();
',
                '<?php use \Exception?>
<?php
$a = new Exception();
',
            ],
            [
                '<?php use \stdClass;
use \DateTime;

?>
<?php
$a = new DateTime();
',
                '<?php use \stdClass; use \DateTime?>
<?php
$a = new DateTime();
', ],
            [
                '<?php namespace Foo;
              '.'
use Bar\Baz;

/**
 * Foo.
 */',
                '<?php namespace Foo;
              '.'
use Bar\Baz;
/**
 * Foo.
 */',
            ],
            [
                '<?php
namespace A\B;

use D;

class C {}
',
                '<?php
namespace A\B;

use D;
class C {}
',
            ],
            [
                '<?php
    namespace A\B;

    use D;

    class C {}
',
                '<?php
    namespace A\B;

    use D;
    class C {}
',
            ],
            [
                '<?php
namespace A\B;

use D;
use E;

class C {}
',
                '<?php
namespace A\B;

use D;
use E;
class C {}
',
            ],
            [
                '<?php
namespace A\B;

use D;

class C {}
',
                '<?php
namespace A\B;

use D; class C {}
',
            ],
            [
                '<?php
namespace A\B;
use D;
use E;

{
    class C {}
}',
                '<?php
namespace A\B;
use D; use E; {
    class C {}
}',
            ],
            [
                '<?php
namespace A\B;
use D;
use E;

{
    class C {}
}',
                '<?php
namespace A\B;
use D;
use E; {
    class C {}
}',
            ],
            [
                '<?php
namespace A\B {
    use D;
    use E;

    class C {}
}',
                '<?php
namespace A\B {
    use D; use E; class C {}
}',
            ],
            [
                '<?php
namespace A\B;
class C {
    use SomeTrait;
}',
            ],
            [
                '<?php
$lambda = function () use (
    $arg
){
    return true;
};',
            ],
            [
                '<?php
namespace A\B;
use D, E;

class C {

}',
                '<?php
namespace A\B;
use D, E;
class C {

}',
            ],
            [
                '<?php
    namespace A1;
    use B1; // need to import this !
    use B2;

    class C1 {}
',
            ],
            [
                '<?php
    namespace A2;
    use B2;// need to import this !
    use B3;

    class C4 {}
',
            ],
            [
                '<?php
namespace A1;
use B1; // need to import this !
use B2;

class C1 {}
',
            ],
            [
                '<?php
namespace A1;
use B1;// need to import this !
use B2;

class C1 {}
',
            ],
            [
                '<?php
namespace A1;
use B1; /** need to import this !*/
use B2;

class C1 {}
',
            ],
            [
                '<?php
namespace A1;
use B1;# need to import this !
use B2;

class C1 {}
',
            ],
            [
                '<?php
namespace Foo;

use Bar;
use Baz;

class Hello {}
',
                '<?php
namespace Foo;

use Bar;
use Baz;


class Hello {}
',
            ],
            [
                '<?php
class HelloTrait {
    use SomeTrait;

    use Another;// ensure use statements for traits are not touched
}
',
            ],
            [
                '<?php
namespace Foo {}
namespace Bar {
    class Baz
    {
        use Aaa;
    }
}
',
            ],
            [
                '<?php use A\B;

?>',
                '<?php use A\B?>',
            ],
            [
                '<?php use A\B;

',
                '<?php use A\B;',
            ],
            [
                str_replace("\n", "\r\n", '<?php
use Foo;
use Bar;

class Baz {}
'),
            ],
            [
                '<?php
use some\test\{ClassA, ClassB, ClassC as C};

?>
test 123
',
                '<?php
use some\test\{ClassA, ClassB, ClassC as C}         ?>
test 123
',
            ],
            [
                '<?php
use some\test\{CA, Cl, ClassC as C};

class Test {}
',
                '<?php
use some\test\{CA, Cl, ClassC as C};
class Test {}
',
            ],
            [
                '<?php
use function some\test\{fn_g, fn_f, fn_e};

fn_a();',
                '<?php
use function some\test\{fn_g, fn_f, fn_e};
fn_a();',
            ],
            [
                '<?php
use const some\test\{ConstA, ConstB, ConstD};

',
                '<?php
use const some\test\{ConstA, ConstB, ConstD};
',
            ],
            [
                '<?php
namespace Z\B;
use const some\test\{ConstA, ConstB, ConstC};
use A\B\C;

',
                '<?php
namespace Z\B;
use const some\test\{ConstA, ConstB, ConstC};
use A\B\C;
',
            ],
            [
                ' <?php
use some\a\ClassA;
use function some\a\fn_a;
use const some\c;

',
                ' <?php
use some\a\ClassA; use function some\a\fn_a; use const some\c;
',
            ],
        ];
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php namespace A\\B;\r\n    use D;\r\n\r\n    class C {}",
                "<?php namespace A\\B;\r\n    use D;\r\n\r\n\r\n    class C {}",
            ],
            [
                "<?php namespace A\\B;\r\n    use D;\r\n\r\n    class C {}",
                "<?php namespace A\\B;\r\n    use D;\r\n    class C {}",
            ],
        ];
    }

    /**
     * @dataProvider provideFix72Cases
     * @requires PHP 7.2
     */
    public function testFix72(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix72Cases(): array
    {
        $imports = [
            'some\a\{ClassA, ClassB, ClassC as C,};',
            'function some\a\{fn_a, fn_b, fn_c,};',
            'const some\a\{ConstA,ConstB,ConstC,};',
            'const some\Z\{ConstX,ConstY,ConstZ,};',
        ];

        $cases = [
            [
                "<?php use some\\a\\{ClassA,};\n\n",
                '<?php use some\a\{ClassA,};',
            ],
            [
                "<?php use some\\a\\{ClassA};\nuse some\\b\\{ClassB};\n\n",
                '<?php use some\a\{ClassA};use some\b\{ClassB};',
            ],
            [
                "<?php use some\\a\\{ClassA};\nuse const some\\b\\{ClassB};\n\n",
                '<?php use some\a\{ClassA};use const some\b\{ClassB};',
            ],
            [
                "<?php use some\\a\\{ClassA, ClassZ};\nuse const some\\b\\{ClassB, ClassX};\nuse function some\\d;\n\n",
                '<?php use some\a\{ClassA, ClassZ};use const some\b\{ClassB, ClassX};use function some\\d;',
            ],
            'group types with trailing comma' => [
                "<?php\nuse ".implode("\nuse ", $imports)."\n\necho 1;",
                "<?php\nuse ".implode('use ', $imports).' echo 1;',
            ],
        ];

        foreach ($imports as $import) {
            $case = [
                "<?php\nuse ".$import."\n\necho 1;",
                "<?php\nuse ".$import.' echo 1;',
            ];

            $cases[] = $case;
            $cases[] = [
                str_replace('some', '\\some', $case[0]),
                str_replace('some', '\\some', $case[1]),
            ];
        }

        return $cases;
    }
}
