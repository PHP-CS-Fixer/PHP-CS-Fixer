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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixer
 */
final class PhpdocInlineTagNormalizerFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php
    /**
     * {link} { LINK }
     * { test }
     * {@inheritDoc rire éclatant des écoliers qui décontenança®¶ñ¿}
     * test other comment
     * {@inheritdoc test} a
     * {@inheritdoc test} b
     * {@inheritdoc test} c
     * {@inheritdoc foo bar.} d
     * {@inheritdoc foo bar.} e
     * {@inheritdoc test} f
     * end comment {@inheritdoc here we are done} @foo {1}
     */
',
            '<?php
    /**
     * {link} { LINK }
     * { test }
     * {@inheritDoc rire éclatant des écoliers qui décontenança®¶ñ¿ }
     * test other comment
     * @{inheritdoc test} a
     * {{@inheritdoc    test}} b
     * {@ inheritdoc   test} c
     * { @inheritdoc 	foo bar.  } d
     * {@ 	inheritdoc foo bar.	} e
     * @{{inheritdoc test}} f
     * end comment {@inheritdoc here we are done} @foo {1}
     */
',
        ];

        yield [
            '<?php
    /**
     * {@foo}
     * @{ bar }
     */',
            '<?php
    /**
     * @{ foo }
     * @{ bar }
     */',
            [
                'tags' => ['foo'],
            ],
        ];

        yield [
            '<?php
    /**
     * @inheritDoc
     * {@inheritDoc}
     */',
            '<?php
    /**
     * @inheritDoc
     * @{ inheritDoc }
     */',
        ];

        foreach (['example', 'id', 'internal', 'inheritdoc', 'link', 'source', 'toc', 'tutorial'] as $tag) {
            yield [
                sprintf("<?php\n     /**\n      * {@%s}a\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * @{%s}a\n      */\n", $tag),
            ];

            yield [
                sprintf("<?php\n     /**\n      * {@%s} b\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * {{@%s}} b\n      */\n", $tag),
            ];

            yield [
                sprintf("<?php\n     /**\n      * c {@%s}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c @{{%s}}\n      */\n", $tag),
            ];

            yield [
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c @{{%s test}}\n      */\n", $tag),
            ];

            // test unbalanced { tags
            yield [
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c {@%s test}}\n      */\n", $tag),
            ];

            yield [
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c {{@%s test}\n      */\n", $tag),
            ];

            yield [
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c {@%s test}}\n      */\n", $tag),
            ];

            yield [
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c @{{%s test}}}\n      */\n", $tag),
            ];
        }

        // don't auto inline tags
        foreach (['example', 'id', 'internal', 'inheritdoc', 'foo', 'link', 'source', 'toc', 'tutorial'] as $tag) {
            yield [
                sprintf("<?php\n     /**\n      * @%s\n      */\n", $tag),
            ];
        }

        // don't touch well formatted tags
        foreach (['example', 'id', 'internal', 'inheritdoc', 'foo', 'link', 'source', 'toc', 'tutorial'] as $tag) {
            yield [
                sprintf("<?php\n     /**\n      * {@%s}\n      */\n", $tag),
            ];
        }

        // invalid syntax
        yield [
            '<?php
    /**
     * {@link https://symfony.com/rfc/rfc1035.text)
     */
    $someVar = "hello";',
        ];

        yield 'do not rename tags' => [
            '<?php
                /**
                 * { @iDontWantToBeChanged }
                 * @Route("/conventions/@{idcc}", name="api_v1_convention_read_by_idcc")
                 *
                 * { @ids }
                 */
            ',
        ];
    }
}
