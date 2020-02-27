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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TransformerInterface;
use PhpCsFixer\Tokenizer\Transformers;

/**
 * @author SpacePossum
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class TransformerTest extends TestCase
{
    /**
     * @dataProvider provideTransformerCases
     */
    public function testTransformersAreFinal(TransformerInterface $transformer)
    {
        $transformerRef = new \ReflectionClass($transformer);

        static::assertTrue(
            $transformerRef->isFinal(),
            sprintf('Transformer "%s" must be declared "final."', $transformer->getName())
        );
    }

    /**
     * @dataProvider provideTransformerCases
     */
    public function testTransformer(TransformerInterface $transformer)
    {
        $name = $transformer->getName();
        $priority = $transformer->getPriority();
        $customTokens = $transformer->getCustomTokens();
        $requiredPhpVersionId = $transformer->getRequiredPhpVersionId();

        static::assertInternalType('string', $name, $name);
        static::assertRegExp('/^[a-z]+[a-z_]*[a-z]$/', $name, $name);

        static::assertInternalType('int', $priority, $name);
        static::assertInternalType('array', $customTokens, $name);

        foreach ($customTokens as $customToken) {
            static::assertInternalType('int', $customToken, $name);
        }

        static::assertInternalType('int', $requiredPhpVersionId, $name);
        static::assertGreaterThanOrEqual(50000, $requiredPhpVersionId, $name);

        $tokens = Tokens::fromCode('<?php ');

        foreach ($tokens as $index => $token) {
            $transformer->process($tokens, $token, $index);
            static::assertFalse($tokens->isChanged());
        }
    }

    /**
     * @return TransformerInterface[]
     */
    public function provideTransformerCases()
    {
        static $transformersArray = null;

        if (null === $transformersArray) {
            $transformers = Transformers::create();
            $reflection = new \ReflectionObject($transformers);
            $builtInTransformers = $reflection->getMethod('findBuiltInTransformers');
            $builtInTransformers->setAccessible(true);
            $transformersArray = [];
            foreach ($builtInTransformers->invoke($transformers) as $transformer) {
                $transformersArray[] = [$transformer];
            }
        }

        return $transformersArray;
    }

    /**
     * @dataProvider provideTransformerPriorityCases
     */
    public function testTransformerPriority(TransformerInterface $first, TransformerInterface $second)
    {
        static::assertLessThan(
            $first->getPriority(),
            $second->getPriority(),
            sprintf('"%s" should have less priority than "%s"', \get_class($second), \get_class($first))
        );
    }

    public function provideTransformerPriorityCases()
    {
        $transformers = [];

        foreach ($this->provideTransformerCases() as list($transformer)) {
            $transformers[$transformer->getName()] = $transformer;
        }

        return [
            [$transformers['curly_brace'], $transformers['brace_class_instantiation']],
            [$transformers['curly_brace'], $transformers['use']],
            [$transformers['return_ref'], $transformers['type_colon']],
            [$transformers['square_brace'], $transformers['brace_class_instantiation']],
            [$transformers['type_colon'], $transformers['nullable_type']],
            [$transformers['use'], $transformers['type_colon']],
        ];
    }
}
