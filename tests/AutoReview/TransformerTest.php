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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\TransformerInterface;
use PhpCsFixer\Tokenizer\Transformers;

/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 */
final class TransformerTest extends TestCase
{
    /**
     * @dataProvider provideTransformerPriorityCases
     */
    public function testTransformerPriority(TransformerInterface $first, TransformerInterface $second): void
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
    public function testTransformerPriorityIsListed(TransformerInterface $transformer): void
    {
        $priority = $transformer->getPriority();

        if (0 === $priority) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $name = $transformer->getName();

        foreach ($this->provideTransformerPriorityCases() as $pair) {
            [$first, $second] = $pair;

            if ($name === $first->getName() || $name === $second->getName()) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        static::fail(sprintf('Transformer "%s" has priority %d but is not in priority test list.', $name, $priority));
    }

    public function provideTransformerPriorityCases(): array
    {
        $transformers = [];

        foreach ($this->provideTransformerCases() as [$transformer]) {
            $transformers[$transformer->getName()] = $transformer;
        }

        return [
            [$transformers['attribute'], $transformers['curly_brace']],
            [$transformers['attribute'], $transformers['square_brace']],
            [$transformers['curly_brace'], $transformers['brace_class_instantiation']],
            [$transformers['curly_brace'], $transformers['import']],
            [$transformers['curly_brace'], $transformers['use']],
            [$transformers['name_qualified'], $transformers['namespace_operator']],
            [$transformers['return_ref'], $transformers['import']],
            [$transformers['return_ref'], $transformers['type_colon']],
            [$transformers['square_brace'], $transformers['brace_class_instantiation']],
            [$transformers['type_colon'], $transformers['named_argument']],
            [$transformers['type_colon'], $transformers['nullable_type']],
            [$transformers['array_typehint'], $transformers['type_alternation']],
            [$transformers['type_colon'], $transformers['type_alternation']],
            [$transformers['array_typehint'], $transformers['type_intersection']],
            [$transformers['type_colon'], $transformers['type_intersection']],
            [$transformers['type_alternation'], $transformers['disjunctive_normal_form_type_parenthesis']],
            [$transformers['use'], $transformers['type_colon']],
        ];
    }

    /**
     * @return TransformerInterface[]
     */
    public static function provideTransformerCases(): array
    {
        static $transformersArray = null;

        if (null === $transformersArray) {
            $transformers = Transformers::createSingleton();
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
