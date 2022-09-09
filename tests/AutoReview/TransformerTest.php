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
    private const PRIORITY_MAP = [
        'array_typehint' => [
            'type_alternation',
            'type_intersection',
        ],
        'attribute' => [
            'curly_brace',
            'disjunctive_normal_form_type_braces',
            'nullable_type',
            'square_brace',
            'type_alternation',
            'type_intersection',
        ],
        'constructor_promotion' => [
            'disjunctive_normal_form_type_braces',
            'nullable_type',
            'type_alternation',
            'type_intersection',
        ],
        'curly_brace' => [
            'brace_class_instantiation',
            'import',
            'square_brace',
            'use',
        ],
        'disjunctive_normal_form_type_braces' => [
            'type_alternation',
            'type_intersection',
        ],
        'name_qualified' => [
            'namespace_operator',
        ],
        'return_ref' => [
            'brace_class_instantiation',
            'import',
            'type_colon',
        ],
        'square_brace' => [
            'brace_class_instantiation',
            'disjunctive_normal_form_type_braces',
        ],
        'type_colon' => [
            'disjunctive_normal_form_type_braces',
            'named_argument',
            'nullable_type',
            'type_alternation',
            'type_intersection',
        ],
        'use' => [
            'type_alternation',
            'type_colon',
            'type_intersection',
        ],
    ];

    /**
     * @requires PHP 8.2
     */
    public function testTransformerPriorities(): void
    {
        $transformerNamePriority = [];
        $transformerNamesWithoutDefaultPriorityNotInPriorityMap = [];

        foreach ($this->getTransformers() as $transformer) {
            $name = $transformer->getName();
            $priority = $transformer->getPriority();
            $transformerNamePriority[$name] = $priority;

            if (0 !== $priority) {
                if (isset(self::PRIORITY_MAP[$name])) {
                    continue;
                }

                foreach (self::PRIORITY_MAP as $beforeNames) {
                    if (\in_array($name, $beforeNames, true)) {
                        continue 2;
                    }
                }

                $transformerNamesWithoutDefaultPriorityNotInPriorityMap[] = $name;
            }
        }

        static::assertEmpty(
            $transformerNamesWithoutDefaultPriorityNotInPriorityMap,
            sprintf("Missing transformers in priority map:\n'%s'.", implode("', '", $transformerNamesWithoutDefaultPriorityNotInPriorityMap))
        );

        $priorityMapConflicts = [];

        foreach (self::PRIORITY_MAP as $name => $beforeNames) {
            foreach ($beforeNames as $beforeName) {
                if ($transformerNamePriority[$name] <= $transformerNamePriority[$beforeName]) {
                    if (!isset($priorityMapConflicts[$name])) {
                        $priorityMapConflicts[$name] = [];
                    }

                    $priorityMapConflicts[$name][] = $beforeName;
                }
            }
        }

        if (0 === \count($priorityMapConflicts)) {
            $this->addToAssertionCount(1);

            return;
        }

        $message = '';

        foreach ($priorityMapConflicts as $name => $conflicts) {
            $message .= sprintf("Priority conflict for '%s' with priority %d:\n", $name, $transformerNamePriority[$name]);

            foreach ($conflicts as $conflict) {
                $message .= sprintf("- '%s' with prio %d\n", $conflict, $transformerNamePriority[$conflict]);
            }
        }

        static::fail($message);
    }

    public function testPriorityMapIsValid(): void
    {
        self::assertArrayIsSorted(array_keys(self::PRIORITY_MAP), 'Priority map top level keys are not sorted.');

        foreach (self::PRIORITY_MAP as $name => $beforeNames) {
            static::assertSame(array_unique($beforeNames), $beforeNames, sprintf('Priority map entry "%s" contains duplicates.', $name));
            self::assertArrayIsSorted($beforeNames, sprintf('Priority map entry "%s" is not sorted.', $name));
        }
    }

    /**
     * @return TransformerInterface[]
     */
    private function getTransformers(): array
    {
        $transformers = Transformers::createSingleton();
        $transformersReflection = new \ReflectionObject($transformers);
        $items = $transformersReflection->getProperty('items');
        $items->setAccessible(true);

        return $items->getValue($transformers);
    }

    /**
     * @param array<int|string, mixed> $array
     */
    private static function assertArrayIsSorted(array $array, string $message): void
    {
        $arraySorted = $array;
        sort($arraySorted);

        static::assertSame($arraySorted, $array, $message);
    }
}
