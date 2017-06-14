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
        $this->assertInstanceOf('\PhpCsFixer\Tokenizer\Transformers', $transformers);
        $this->assertSame($transformers, Transformers::create());
    }

    public function testTransform()
    {
        $transformersClasses = array(
            'PhpCsFixer\Tokenizer\Transformer\ArrayTypehintTransformer',
            'PhpCsFixer\Tokenizer\Transformer\BraceClassInstantiationTransformer',
            'PhpCsFixer\Tokenizer\Transformer\ClassConstantTransformer',
            'PhpCsFixer\Tokenizer\Transformer\CurlyBraceTransformer',
            'PhpCsFixer\Tokenizer\Transformer\ImportTransformer',
            'PhpCsFixer\Tokenizer\Transformer\NamespaceOperatorTransformer',
            'PhpCsFixer\Tokenizer\Transformer\NullableTypeTransformer',
            'PhpCsFixer\Tokenizer\Transformer\ReturnRefTransformer',
            'PhpCsFixer\Tokenizer\Transformer\SquareBraceTransformer',
            'PhpCsFixer\Tokenizer\Transformer\TypeAlternationTransformer',
            'PhpCsFixer\Tokenizer\Transformer\TypeColonTransformer',
            'PhpCsFixer\Tokenizer\Transformer\UseTransformer',
            'PhpCsFixer\Tokenizer\Transformer\WhitespacyCommentTransformer',
        );

        $transformers = Transformers::create();
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

        $this->assertCount(0, $classes, sprintf("Following transformers are not loaded:\n%s", implode("\n", array_keys($classes))));
    }
}
