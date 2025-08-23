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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class BlankLineAfterNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php namespace A\B?>
                <?php
                    for($i=0; $i<10; ++$i) {echo $i;}',
        ];

        yield [
            '<?php namespace A\B?>',
        ];

        yield [
            '<?php
namespace A\B;

class C {}
',
            '<?php
namespace A\B;



class C {}
',
        ];

        yield [
            '<?php
namespace A\B;

class C {}
',
            '<?php
namespace A\B;
class C {}
',
        ];

        yield [
            '<?php
namespace A\B;

class C {}
',
            '<?php
namespace A\B;  class C {}
',
        ];

        yield [
            '<?php
namespace A\B;

class C {}
',
            '<?php
namespace A\B;class C {}
',
        ];

        yield [
            '<?php
namespace A\B {
    class C {
        public $foo;
        private $bar;
    }
}
',
        ];

        yield [
            "<?php\rnamespace A\\B;

class C {}\r",
            "<?php\rnamespace A\\B;\r\r\r\r\r\rclass C {}\r",
        ];

        yield [
            '<?php
namespace A\B;

namespace\C\func();
foo();
',
        ];

        yield [
            '<?php
namespace Foo;
',
            '<?php
namespace Foo;



',
        ];

        yield [
            '<?php
namespace Foo;
',
            '<?php
namespace Foo;',
        ];

        yield [
            '<?php
namespace Foo;

?>',
            '<?php
namespace Foo;



?>',
        ];

        yield [
            '<?php
    namespace Foo;

    class Bar {}',
        ];

        yield [
            '<?php
  namespace Foo;

      class Bar {}',
            '<?php
  namespace Foo;
      class Bar {}',
        ];

        yield [
            '<?php
    namespace My\NS;

    class X extends Y {}',
        ];

        yield [
            '<?php
namespace My\NS; // comment

class X extends Y {}',
        ];

        yield [
            '<?php
namespace My\NS; /* comment */

class X extends Y {}',
        ];

        yield [
            '<?php
namespace My\NS; /*
comment 1
comment 2
*/

class X extends Y {}',
            '<?php
namespace My\NS; /*
comment 1
comment 2
*/
class X extends Y {}',
        ];

        yield [
            '<?php
namespace My\NS; /** comment */

class X extends Y {}',
        ];

        yield [
            '<?php
namespace My\NS; /**
comment 1
comment 2
*/

class X extends Y {}',
            '<?php
namespace My\NS; /**
comment 1
comment 2
*/
class X extends Y {}',
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php namespace A\\B;\r\n\r\nclass C {}",
            '<?php namespace A\B;  class C {}',
        ];

        yield [
            "<?php namespace A\\B;\r\n\r\nclass C {}",
            "<?php namespace A\\B;\r\n\r\n\r\n\r\n\r\n\r\nclass C {}",
        ];
    }
}
