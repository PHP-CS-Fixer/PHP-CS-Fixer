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

namespace PhpCsFixer\Tests\Tokenizer;

use PhpCsFixer\Test\AccessibleObject;
use PhpCsFixer\Tokenizer\Transformers;
use PHPUnit\Framework\TestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformers
 */
final class TransformersTest extends TestCase
{
    public function testCreateOneTime()
    {
        $transformers = Transformers::create();
        $this->assertSame($transformers, Transformers::create());
    }

    public function testTransform()
    {
        $transformers = Transformers::create();
        $transformersClasses = array(
            \PhpCsFixer\Tokenizer\Transformer\CurlyBraceTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\TypeColonTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\NullableTypeTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\BraceClassInstantiationTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\UseTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\TypeAlternationTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\WhitespacyCommentTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\ImportTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\ArrayTypehintTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\SquareBraceTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\NamespaceOperatorTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\ClassConstantTransformer::class,
            \PhpCsFixer\Tokenizer\Transformer\ReturnRefTransformer::class,
        );

        $classes = array();
        foreach ($transformersClasses as $class) {
            $this->assertTrue(class_exists($class, false), sprintf('Failed transformer "%s" is loaded by Transformers.', $class));
            $transformer = new $class();
            if (PHP_VERSION_ID >= $transformer->getRequiredPhpVersionId()) {
                $classes[$class] = true;
            }
        }

        $accessible = new AccessibleObject($transformers);
        /** @var array $transformers */
        $transformers = $accessible->items;

        $this->assertInternalType('array', $transformers);
        $this->assertCount(count($classes), $transformers);
        $this->assertContainsOnlyInstancesOf('PhpCsFixer\Tokenizer\TransformerInterface', $transformers);

        foreach ($transformers as $transformer) {
            unset($classes[get_class($transformer)]);
        }

        $this->assertCount(0, $classes);
    }
}
