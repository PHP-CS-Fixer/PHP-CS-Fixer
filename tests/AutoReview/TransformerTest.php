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

    /**
     * @dataProvider provideTransformerCases
     */
    public function testTransformerPriorityIsListed(TransformerInterface $transformer)
    {
        $priority = $transformer->getPriority();

        if (0 === $priority) {
            $this->addToAssertionCount(1);

            return;
        }

        $name = $transformer->getName();

        foreach ($this->provideTransformerPriorityCases() as $pair) {
            list($first, $second) = $pair;

            if ($name === $first->getName() || $name === $second->getName()) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        static::fail(sprintf('Transformer "%s" has priority %d but is not in priority test list.', $name, $priority));
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
            [$transformers['name_qualified'], $transformers['namespace_operator']],
        ];
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
}
