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
use PhpCsFixer\Utils;

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
    (?<types> # several types separated by `|` or `&`
'.self::REGEX_TYPE.'
        (?:
            \h*(?<glue>[|&])\h*
            (?&type)
        )*+
    )';

    private const REGEX_TYPE = '
        (?<type> # single type
            (?<nullable>\??\h*)
            (?:
                (?<object_like_array>
                    (?<object_like_array_start>(?:array|list|object)\h*\{\h*)
                        (?<object_like_array_inners>
                            (?<object_like_array_inner>
                                (?<object_like_array_inner_key>(?:(?&constant)|(?&name))\h*\??\h*:\h*)?
                                (?<object_like_array_inner_value>(?&types_inner))
                            )
                            (?:
                                \h*,\h*
                                (?&object_like_array_inner)
                            )*
                            (?:\h*,\h*)?
                        )?
                    \h*\}
                )
                |
                (?<callable> # callable syntax, e.g. `callable(string): bool`
                    (?<callable_start>(?:callable|\\\\?Closure)\h*\(\h*)
                        (?<callable_arguments>
                            (?&types_inner)
                            (?:
                                \h*,\h*
                                (?&types_inner)
                            )*
                            (?:\h*,\h*)?
                        )?
                    \h*\)
                    (?:
                        \h*\:\h*
                        (?<callable_return>(?&type))
                    )?
                )
                |
                (?<generic> # generic syntax, e.g.: `array<int, \Foo\Bar>`
                    (?<generic_start>
                        (?&name)+
                        \h*<\h*
                    )
                        (?<generic_types>
                            (?&types_inner)
                            (?:
                                \h*,\h*
                                (?&types_inner)
                            )*
                        )
                    \h*>
                )
                |
                (?<class_constant> # class constants with optional wildcard, e.g.: `Foo::*`, `Foo::CONST_A`, `FOO::CONST_*`
                    (?&name)::\*?(?:(?&identifier)\*?)*
                )
                |
                (?<constant> # single constant value (case insensitive), e.g.: 1, -1.8E+6, `\'a\'`
                    (?i)
                    null | true | false
                    # all sorts of numbers: with or without sign, supports literal separator and several numeric systems,
                    # e.g.: 1, +1.1, 1., .1, -1, 123E+8, 123_456_789, 0x7Fb4, 0b0110, 0o777
                    | [+-]?(?:
                        (?:0b[01]++(?:_[01]++)*+)
                        | (?:0o[0-7]++(?:_[0-7]++)*+)
                        | (?:0x[\da-f]++(?:_[\da-f]++)*+)
                        | (?:(?<constant_digits>\d++(?:_\d++)*+)|(?=\.\d))
                          (?:\.(?&constant_digits)|(?<=\d)\.)?+
                          (?:e[+-]?(?&constant_digits))?+
                    )
                    | \'(?:[^\'\\\\]|\\\\.)*+\'
                    | "(?:[^"\\\\]|\\\\.)*+"
                    | [@$]?(?:this | self | static)
                    (?-i)
                )
                |
                (?<name> # full name, e.g.: `int`, `\DateTime`, `\Foo\Bar`
                    \\\\?+
                    (?<identifier>(?!(?<!\*)\d)[^\x00-\x2f\x3a-\x40\x5b-\x5e\x60\x7b-\x7f]++)
                    (?:[\\\\\-](?&identifier))*+
                )
                |
                (?<parenthesized> # parenthesized type, e.g.: `(int)`, `(int|\stdClass)`
                    (?<parenthesized_start>
                        \(\h*
                    )
                    (?:
                        (?<parenthesized_types>
                            (?&types_inner)
                        )
                        |
                        (?<conditional> # conditional type, e.g.: `$foo is \Throwable ? false : $foo`
                            (?<conditional_cond_left>
                                (?:\$(?&identifier))
                                |
                                (?<conditional_cond_left_types>(?&types_inner))
                            )
                            (?<conditional_cond_middle>
                                \h+(?i)is(?:\h+not)?(?-i)\h+
                            )
                            (?<conditional_cond_right_types>(?&types_inner))
                            (?<conditional_true_start>\h*\?\h*)
                            (?<conditional_true_types>(?&types_inner))
                            (?<conditional_false_start>\h*:\h*)
                            (?<conditional_false_types>(?&types_inner))
                        )
                    )
                    \h*\)
                )
            )
            (?<array> # array, e.g.: `string[]`, `array<int, string>[][]`
                (\h*\[\h*\])*
            )
            (?:(?=1)0
                (?<types_inner>
                    (?&type)
                    (?:
                        \h*[|&]\h*
                        (?&type)
                    )*+
                )
            |)
        )';

    private string $value;

    private bool $isUnionType = false;

    /**
     * @var list<array{start_index: int, expression: self}>
     */
    private array $innerTypeExpressions = [];

    private string $typesGlue = '|';

    private ?NamespaceAnalysis $namespace;

    /**
     * @var NamespaceUseAnalysis[]
     */
    private array $namespaceUses;

    /**
     * @param NamespaceUseAnalysis[] $namespaceUses
     */
    public function __construct(string $value, ?NamespaceAnalysis $namespace, array $namespaceUses)
    {
        $this->value = $value;
        $this->namespace = $namespace;
        $this->namespaceUses = $namespaceUses;

        $this->parse();
    }

    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        if ($this->isUnionType) {
            return array_map(
                static fn (array $type) => $type['expression']->toString(),
                $this->innerTypeExpressions,
            );
        }

        return [$this->value];
    }

    /**
     * @param callable(self $a, self $b): int $compareCallback
     */
    public function sortTypes(callable $compareCallback): void
    {
        foreach (array_reverse($this->innerTypeExpressions) as [
            'start_index' => $startIndex,
            'expression' => $inner,
        ]) {
            $initialValueLength = \strlen($inner->toString());

            $inner->sortTypes($compareCallback);

            $this->value = substr_replace(
                $this->value,
                $inner->toString(),
                $startIndex,
                $initialValueLength
            );
        }

        if ($this->isUnionType) {
            $this->innerTypeExpressions = Utils::stableSort(
                $this->innerTypeExpressions,
                static fn (array $type): self => $type['expression'],
                $compareCallback,
            );

            $this->value = implode($this->getTypesGlue(), $this->getTypes());
        }
    }

    public function getTypesGlue(): string
    {
        return $this->typesGlue;
    }

    public function getCommonType(): ?string
    {
        $aliases = $this->getAliases();

        $mainType = null;

        foreach ($this->getTypes() as $type) {
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
        foreach ($this->getTypes() as $type) {
            if (\in_array($type, ['null', 'mixed'], true)) {
                return true;
            }
        }

        return false;
    }

    private function parse(): void
    {
        $index = 0;
        while (true) {
            Preg::match(
                '{\G'.self::REGEX_TYPE.'(?:\h*(?<glue>[|&])\h*|$)}x',
                $this->value,
                $matches,
                0,
                $index
            );

            if ([] === $matches) { // invalid phpdoc type
                // TODO once all phpdoc types are parsed strictly using self::REGEX_TYPES,
                // the parse cannot fail and we can throw here safely
                return;
            }

            if (!$this->isUnionType) {
                if (($matches['glue'] ?? '') === '') {
                    break;
                }

                $this->isUnionType = true;
                $this->typesGlue = $matches['glue'];
            }

            $this->innerTypeExpressions[] = [
                'start_index' => $index,
                'expression' => $this->inner($matches['type']),
            ];

            $consumedValueLength = \strlen($matches[0]);
            $index += $consumedValueLength;

            if (\strlen($this->value) === $index) {
                return;
            }
        }

        $index = '' !== $matches['nullable'] ? 1 : 0;

        if ('' !== ($matches['generic'] ?? '')) {
            $this->parseCommaSeparatedInnerTypes(
                $index + \strlen($matches['generic_start']),
                $matches['generic_types']
            );

            return;
        }

        if ('' !== ($matches['callable'] ?? '')) {
            $this->parseCommaSeparatedInnerTypes(
                $index + \strlen($matches['callable_start']),
                $matches['callable_arguments'] ?? ''
            );

            if ('' !== ($matches['callable_return'] ?? '')) {
                $this->innerTypeExpressions[] = [
                    'start_index' => \strlen($this->value) - \strlen($matches['callable_return']),
                    'expression' => $this->inner($matches['callable_return']),
                ];
            }

            return;
        }

        if ('' !== ($matches['object_like_array'] ?? '')) {
            $this->parseObjectLikeArrayInnerTypes(
                $index + \strlen($matches['object_like_array_start']),
                $matches['object_like_array_inners'] ?? ''
            );

            return;
        }

        if ('' !== ($matches['parenthesized'] ?? '')) {
            $index += \strlen($matches['parenthesized_start']);

            if ('' !== ($matches['conditional'] ?? '')) {
                if ('' !== ($matches['conditional_cond_left_types'] ?? '')) {
                    $this->innerTypeExpressions[] = [
                        'start_index' => $index,
                        'expression' => $this->inner($matches['conditional_cond_left_types']),
                    ];
                }

                $index += \strlen($matches['conditional_cond_left']) + \strlen($matches['conditional_cond_middle']);

                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['conditional_cond_right_types']),
                ];

                $index += \strlen($matches['conditional_cond_right_types']) + \strlen($matches['conditional_true_start']);

                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['conditional_true_types']),
                ];

                $index += \strlen($matches['conditional_true_types']) + \strlen($matches['conditional_false_start']);

                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['conditional_false_types']),
                ];
            } else {
                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['parenthesized_types']),
                ];
            }

            return;
        }
    }

    private function parseCommaSeparatedInnerTypes(int $startIndex, string $value): void
    {
        $index = 0;
        while (\strlen($value) !== $index) {
            Preg::match(
                '{\G'.self::REGEX_TYPES.'(?:\h*,\h*|$)}x',
                $value,
                $matches,
                0,
                $index
            );

            $this->innerTypeExpressions[] = [
                'start_index' => $startIndex + $index,
                'expression' => $this->inner($matches['types']),
            ];

            $index += \strlen($matches[0]);
        }
    }

    private function parseObjectLikeArrayInnerTypes(int $startIndex, string $value): void
    {
        $index = 0;
        while (\strlen($value) !== $index) {
            Preg::match(
                '{\G(?:(?=1)0'.self::REGEX_TYPES.'|(?<_object_like_array_inner>(?&object_like_array_inner))(?:\h*,\h*|$))}x',
                $value,
                $prematches,
                0,
                $index
            );
            $consumedValue = $prematches['_object_like_array_inner'];
            $consumedValueLength = \strlen($consumedValue);
            $consumedCommaLength = \strlen($prematches[0]) - $consumedValueLength;

            $addedPrefix = 'array{';
            Preg::match(
                '{^'.self::REGEX_TYPES.'$}x',
                $addedPrefix.$consumedValue.'}',
                $matches,
                PREG_OFFSET_CAPTURE
            );

            $this->innerTypeExpressions[] = [
                'start_index' => $startIndex + $index + $matches['object_like_array_inner_value'][1] - \strlen($addedPrefix),
                'expression' => $this->inner($matches['object_like_array_inner_value'][0]),
            ];

            $index += $consumedValueLength + $consumedCommaLength;
        }
    }

    private function inner(string $value): self
    {
        return new self($value, $this->namespace, $this->namespaceUses);
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

        if (null === $this->namespace || $this->namespace->isGlobalNamespace()) {
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
