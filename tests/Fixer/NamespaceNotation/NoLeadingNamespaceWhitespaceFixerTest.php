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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideExamples()
    {
        $manySpaces = [];
        for ($i = 1; $i <= 100; ++$i) {
            $manySpaces[] = 'namespace Test'.$i.';';
        }

        return [
            // with newline
            ["<?php\nnamespace Test1;"],
            ["<?php\n\nnamespace Test2;"],
            [
                "<?php\nnamespace Test3;",
                "<?php\n namespace Test3;",
            ],
            // without newline
            ['<?php namespace Test4;'],
            [
                '<?php namespace Test5;',
                '<?php  namespace Test5;',
            ],
            // multiple namespaces with newline
            [
                '<?php
namespace Test6a;
namespace Test6b;',
            ],
            [
                '<?php
namespace Test7a;
/* abc */
namespace Test7b;',
                '<?php
namespace Test7a;
/* abc */namespace Test7b;',
            ],
            [
                '<?php
namespace Test8a;
namespace Test8b;',
                '<?php
 namespace Test8a;
    namespace Test8b;',
            ],
            [
                '<?php
namespace Test9a;
class Test {}
namespace Test9b;',
                '<?php
 namespace Test9a;
class Test {}
   namespace Test9b;',
            ],
            [
                '<?php
namespace Test10a;
use Exception;
namespace Test10b;',
                '<?php
 namespace Test10a;
use Exception;
   namespace Test10b;',
            ],
            // multiple namespaces without newline
            ['<?php namespace Test11a; namespace Test11b;'],
            [
                '<?php namespace Test12a; namespace Test12b;',
                '<?php    namespace Test12a;  namespace Test12b;', ],
            [
                '<?php namespace Test13a; namespace Test13b;',
                '<?php namespace Test13a;  namespace Test13b;', ],
            // namespaces without spaces in between
            [
                '<?php
namespace Test14a{}
namespace Test14b{}',
                '<?php
     namespace Test14a{}namespace Test14b{}',
            ],
            [
                '<?php
namespace Test15a;
namespace Test15b;',
                '<?php
namespace Test15a;namespace Test15b;',
            ],
            [
                '<?php
'.implode("\n", $manySpaces),
                '<?php
'.implode('', $manySpaces),
            ],
            [
                '<?php
#
namespace TestComment;',
                '<?php
#
  namespace TestComment;',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\r\nnamespace TestW1a{}\r\nnamespace TestW1b{}",
                "<?php\r\n     namespace TestW1a{}\r\nnamespace TestW1b{}",
            ],
            [
                "<?php\r\nnamespace Test14a{}\r\nnamespace Test14b{}",
                "<?php\r\n     namespace Test14a{}namespace Test14b{}",
            ],
        ];
    }
}
