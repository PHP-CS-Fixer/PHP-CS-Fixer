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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author VeeWee <toonverwerft@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer
 */
final class NamespacesAnalyzerTest extends TestCase
{
    /**
     * @param string $code
     * @param array  $expected
     * @dataProvider provideNamespacesCases
     */
    public function testNamespaces($code, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespacesAnalyzer();

        $this->assertSame($expected, $analyzer->getDeclarations($tokens));
    }

    public function provideNamespacesCases()
    {
        return [
            ['<?php // no namespaces', []],
            ['<?php namespace Foo\Bar;', [
                [
                    'fullName' => 'Foo\Bar',
                    'shortName' => 'Bar',
                    'start' => 1,
                    'end' => 6,
                ],
            ]],
            ['<?php namespace Foo\Bar{}; namespace Foo\Baz {};', [
                [
                    'fullName' => 'Foo\Bar',
                    'shortName' => 'Bar',
                    'start' => 1,
                    'end' => 6,
                ],
                [
                    'fullName' => 'Foo\Baz',
                    'shortName' => 'Baz',
                    'start' => 10,
                    'end' => 16,
                ],
            ]],
        ];
    }
}
