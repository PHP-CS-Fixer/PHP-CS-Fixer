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
        self::assertLessThan(
            $first->getPriority(),
            $second->getPriority(),
            sprintf('"%s" should have less priority than "%s"', \get_class($second), \get_class($first))
        );
    }

    /**
     * @dataProvider provideTransformerPriorityIsListedCases
     */
    public function testTransformerPriorityIsListed(TransformerInterface $transformer): void
    {
        $priority = $transformer->getPriority();

        if (0 === $priority) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $name = $transformer->getName();

        foreach (self::provideTransformerPriorityCases() as $pair) {
            [$first, $second] = $pair;

            if ($name === $first->getName() || $name === $second->getName()) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        self::fail(sprintf('Transformer "%s" has priority %d but is not in priority test list.', $name, $priority));
    }

    public static function provideTransformerPriorityCases(): iterable
    {
        $transformers = [];

        foreach (self::provideTransformerPriorityIsListedCases() as [$transformer]) {
            $transformers[$transformer->getName()] = $transformer;
        }

        yield [$transformers['attribute'], $transformers['brace']];

        yield [$transformers['attribute'], $transformers['square_brace']];

        yield [$transformers['brace'], $transformers['brace_class_instantiation']];

        yield [$transformers['brace'], $transformers['import']];

        yield [$transformers['brace'], $transformers['use']];

        yield [$transformers['name_qualified'], $transformers['namespace_operator']];

        yield [$transformers['return_ref'], $transformers['import']];

        yield [$transformers['return_ref'], $transformers['type_colon']];

        yield [$transformers['square_brace'], $transformers['brace_class_instantiation']];

        yield [$transformers['type_colon'], $transformers['named_argument']];

        yield [$transformers['type_colon'], $transformers['nullable_type']];

        yield [$transformers['array_typehint'], $transformers['type_alternation']];

        yield [$transformers['type_colon'], $transformers['type_alternation']];

        yield [$transformers['array_typehint'], $transformers['type_intersection']];

        yield [$transformers['type_colon'], $transformers['type_intersection']];

        yield [$transformers['type_alternation'], $transformers['disjunctive_normal_form_type_parenthesis']];

        yield [$transformers['use'], $transformers['type_colon']];
    }

    /**
     * @return iterable<array{TransformerInterface}>
     */
    public static function provideTransformerPriorityIsListedCases(): iterable
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
