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

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

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
     *
     * @dataProvider provideNamespacesCases
     */
    public function testNamespaces($code, $expected)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new NamespacesAnalyzer();

        $this->assertSame(serialize($expected), serialize(($analyzer->getDeclarations($tokens))));
    }

    public function provideNamespacesCases()
    {
        return [
            ['<?php // no namespaces', []],
            ['<?php namespace Foo\Bar;', [
                new NamespaceAnalysis(
                    'Foo\Bar',
                    'Bar',
                    1,
                    6
                ),
            ]],
            ['<?php namespace Foo\Bar{}; namespace Foo\Baz {};', [
                new NamespaceAnalysis(
                    'Foo\Bar',
                    'Bar',
                    1,
                    6
                ),
                new NamespaceAnalysis(
                    'Foo\Baz',
                    'Baz',
                    10,
                    16
                ),
            ]],
        ];
    }
}
