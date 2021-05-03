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

namespace PhpCsFixer\DocBlock;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

/**
 * @internal
 */
final class TypeExpression
{
    /**
     * Regex to match any types, shall be used with `x` modifier.
     */
    const REGEX_TYPES = '
    # <simple> is any non-array, non-generic, non-alternated type, eg `int` or `\Foo`
    # <array> is array of <simple>, eg `int[]` or `\Foo[]`
    # <generic> is generic collection type, like `array<string, int>`, `Collection<Item>` and more complex like `Collection<int, \null|SubCollection<string>>`
    # <type> is <simple>, <array> or <generic> type, like `int`, `bool[]` or `Collection<ItemKey, ItemVal>`
    # <types> is one or more types alternated via `|`, like `int|bool[]|Collection<ItemKey, ItemVal>`
    (?<types>
        (?<type>
            (?<array>
                (?&simple)(\[\])*
            )
            |
            (?<simple>
                [@$?]?[\\\\\w]+
            )
            |
            (?<generic>
                (?&simple)
                <
                    (?:(?&types),\s*)?(?:(?&types)|(?&generic))
                >
            )
        )
        (?:
            \|
            (?:(?&simple)|(?&array)|(?&generic))
        )*
    )
    ';

    /**
     * @var string[]
     */
    private $types = [];

    /**
     * @var null|NamespaceAnalysis
     */
    private $namespace;

    /**
     * @var NamespaceUseAnalysis[]
     */
    private $namespaceUses;

    /**
     * @param string                 $value
     * @param null|NamespaceAnalysis $namespace
     * @param NamespaceUseAnalysis[] $namespaceUses
     */
    public function __construct($value, $namespace, array $namespaceUses)
    {
        while ('' !== $value && false !== $value) {
            Preg::match(
                '{^'.self::REGEX_TYPES.'$}x',
                $value,
                $matches
            );

            $this->types[] = $matches['type'];
            $value = substr($value, \strlen($matches['type']) + 1);
        }

        $this->namespace = $namespace;
        $this->namespaceUses = $namespaceUses;
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return null|string
     */
    public function getCommonType()
    {
        $aliases = [
            'true' => 'bool',
            'false' => 'bool',
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'real' => 'float',
            'callback' => 'callable',
        ];

        $mainType = null;

        foreach ($this->types as $type) {
            if ('null' === $type) {
                continue;
            }

            if (isset($aliases[$type])) {
                $type = $aliases[$type];
            } elseif (1 === Preg::match('/\[\]$/', $type)) {
                $type = 'array';
            } elseif (1 === Preg::match('/^(.+?)</', $type, $matches)) {
                $type = $matches[1];
            }

            if (null === $mainType || $type === $mainType) {
                $mainType = $type;

                continue;
            }

            $mainType = $this->getParentType($type, $mainType);

            if (null === $mainType) {
                return null;
            }
        }

        return $mainType;
    }

    /**
     * @return bool
     */
    public function allowsNull()
    {
        foreach ($this->types as $type) {
            if (\in_array($type, ['null', 'mixed'], true)) {
                return true;
            }
        }

        return false;
    }

    private function getParentType($type1, $type2)
    {
        $types = [
            $this->normalize($type1),
            $this->normalize($type2),
        ];
        natcasesort($types);
        $types = implode('|', $types);

        $parents = [
            'array|iterable' => 'iterable',
            'array|Traversable' => 'iterable',
            'iterable|Traversable' => 'iterable',
            'self|static' => 'self',
        ];

        if (isset($parents[$types])) {
            return $parents[$types];
        }

        return null;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function normalize($type)
    {
        $aliases = [
            'true' => 'bool',
            'false' => 'bool',
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'real' => 'float',
            'callback' => 'callable',
        ];

        if (isset($aliases[$type])) {
            return $aliases[$type];
        }

        if (\in_array($type, [
            'void',
            'null',
            'bool',
            'int',
            'float',
            'string',
            'array',
            'iterable',
            'object',
            'callable',
            'resource',
            'mixed',
        ], true)) {
            return $type;
        }

        if (1 === Preg::match('/\[\]$/', $type)) {
            return 'array';
        }

        if (1 === Preg::match('/^(.+?)</', $type, $matches)) {
            return $matches[1];
        }

        if (0 === strpos($type, '\\')) {
            return substr($type, 1);
        }

        foreach ($this->namespaceUses as $namespaceUse) {
            if ($namespaceUse->getShortName() === $type) {
                return $namespaceUse->getFullName();
            }
        }

        if (null === $this->namespace || '' === $this->namespace->getShortName()) {
            return $type;
        }

        return "{$this->namespace->getFullName()}\\{$type}";
    }
}
