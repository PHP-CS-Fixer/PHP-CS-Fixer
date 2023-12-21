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
 * @author Bram Gotink <bram@gotink.me>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer
 */
final class NoLeadingNamespaceWhitespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        $manySpaces = [];

        for ($i = 1; $i <= 100; ++$i) {
            $manySpaces[] = 'namespace Test'.$i.';';
        }

        // with newline
        yield ["<?php\nnamespace Test1;"];

        yield ["<?php\n\nnamespace Test2;"];

        yield [
            "<?php\nnamespace Test3;",
            "<?php\n namespace Test3;",
        ];

        // without newline
        yield ['<?php namespace Test4;'];

        yield [
            '<?php namespace Test5;',
            '<?php  namespace Test5;',
        ];

        // multiple namespaces with newline
        yield [
            '<?php
namespace Test6a;
namespace Test6b;',
        ];

        yield [
            '<?php
namespace Test7a;
/* abc */
namespace Test7b;',
            '<?php
namespace Test7a;
/* abc */namespace Test7b;',
        ];

        yield [
            '<?php
namespace Test8a;
namespace Test8b;',
            '<?php
 namespace Test8a;
    namespace Test8b;',
        ];

        yield [
            '<?php
namespace Test9a;
class Test {}
namespace Test9b;',
            '<?php
 namespace Test9a;
class Test {}
   namespace Test9b;',
        ];

        yield [
            '<?php
namespace Test10a;
use Exception;
namespace Test10b;',
            '<?php
 namespace Test10a;
use Exception;
   namespace Test10b;',
        ];

        // multiple namespaces without newline
        yield ['<?php namespace Test11a; namespace Test11b;'];

        yield [
            '<?php namespace Test12a; namespace Test12b;',
            '<?php    namespace Test12a;  namespace Test12b;', ];

        yield [
            '<?php namespace Test13a; namespace Test13b;',
            '<?php namespace Test13a;  namespace Test13b;', ];

        // namespaces without spaces in between
        yield [
            '<?php
namespace Test14a{}
namespace Test14b{}',
            '<?php
     namespace Test14a{}namespace Test14b{}',
        ];

        yield [
            '<?php
namespace Test15a;
namespace Test15b;',
            '<?php
namespace Test15a;namespace Test15b;',
        ];

        yield [
            '<?php
'.implode("\n", $manySpaces),
            '<?php
'.implode('', $manySpaces),
        ];

        yield [
            '<?php
#
namespace TestComment;',
            '<?php
#
  namespace TestComment;',
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

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php\r\nnamespace TestW1a{}\r\nnamespace TestW1b{}",
            "<?php\r\n     namespace TestW1a{}\r\nnamespace TestW1b{}",
        ];

        yield [
            "<?php\r\nnamespace Test14a{}\r\nnamespace Test14b{}",
            "<?php\r\n     namespace Test14a{}namespace Test14b{}",
        ];
    }
}
