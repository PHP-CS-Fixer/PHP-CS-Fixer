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
     *
     * @internal
     */
    public const REGEX_TYPES = '
    (?<types> # alternation of several types separated by `|`
        (?<type> # single type
            \?? # optionally nullable
            (?:
                (?<object_like_array>
                    array\h*\{
                        (?<object_like_array_key>
                            \h*[^?:\h]+\h*\??\h*:\h*(?&types)
                        )
                        (?:\h*,(?&object_like_array_key))*
                    \h*\}
                )
                |
                (?<callable> # callable syntax, e.g. `callable(string): bool`
                    (?:callable|Closure)\h*\(\h*
                        (?&types)
                        (?:
                            \h*,\h*
                            (?&types)
                        )*
                    \h*\)
                    (?:
                        \h*\:\h*
                        (?&types)
                    )?
                )
                |
                (?<generic> # generic syntax, e.g.: `array<int, \Foo\Bar>`
                    (?&name)+
                    \h*<\h*
                        (?&types)
                        (?:
                            \h*,\h*
                            (?&types)
                        )*
                    \h*>
                )
                |
                (?<class_constant> # class constants with optional wildcard, e.g.: `Foo::*`, `Foo::CONST_A`, `FOO::CONST_*`
                    (?&name)::(\*|\w+\*?)
                )
                |
                (?<array> # array expression, e.g.: `string[]`, `string[][]`
                    (?&name)(\[\])+
                )
                |
                (?<constant> # single constant value (case insensitive), e.g.: 1, `\'a\'`
                    (?i)
                    null | true | false
                    | [\d.]+
                    | \'[^\']+?\' | "[^"]+?"
                    | [@$]?(?:this | self | static)
                    (?-i)
                )
                |
                (?<name> # single type, e.g.: `null`, `int`, `\Foo\Bar`
                    [\\\\\w-]++
                )
            )
            (?: # intersection
                \h*&\h*
                (?&type)
            )*
        )
        (?:
            \h*\|\h*
            (?&type)
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
     * @param NamespaceUseAnalysis[] $namespaceUses
     */
    public function __construct(string $value, ?NamespaceAnalysis $namespace, array $namespaceUses)
    {
        while ('' !== $value) {
            Preg::match(
                '{^'.self::REGEX_TYPES.'$}x',
                $value,
                $matches
            );

            $this->types[] = $matches['type'];
            $value = Preg::replace(
                '/^'.preg_quote($matches['type'], '/').'(\h*\|\h*)?/',
                '',
                $value
            );
        }

        $this->namespace = $namespace;
        $this->namespaceUses = $namespaceUses;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function getCommonType(): ?string
    {
        $aliases = $this->getAliases();

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

    public function allowsNull(): bool
    {
        foreach ($this->types as $type) {
            if (\in_array($type, ['null', 'mixed'], true)) {
                return true;
            }
        }

        return false;
    }

    private function getParentType(string $type1, string $type2): ?string
    {
        $types = [
            $this->normalize($type1),
            $this->normalize($type2),
        ];
        natcasesort($types);
        $types = implode('|', $types);

        $parents = [
            'array|Traversable' => 'iterable',
            'array|iterable' => 'iterable',
            'iterable|Traversable' => 'iterable',
            'self|static' => 'self',
        ];

        return $parents[$types] ?? null;
    }

    private function normalize(string $type): string
    {
        $aliases = $this->getAliases();

        if (isset($aliases[$type])) {
            return $aliases[$type];
        }

        if (\in_array($type, [
            'array',
            'bool',
            'callable',
            'float',
            'int',
            'iterable',
            'mixed',
            'never',
            'null',
            'object',
            'resource',
            'string',
            'void',
        ], true)) {
            return $type;
        }

        if (1 === Preg::match('/\[\]$/', $type)) {
            return 'array';
        }

        if (1 === Preg::match('/^(.+?)</', $type, $matches)) {
            return $matches[1];
        }

        if (str_starts_with($type, '\\')) {
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

    /**
     * @return array<string,string>
     */
    private function getAliases(): array
    {
        return [
            'boolean' => 'bool',
            'callback' => 'callable',
            'double' => 'float',
            'false' => 'bool',
            'integer' => 'int',
            'real' => 'float',
            'true' => 'bool',
        ];
    }
}
