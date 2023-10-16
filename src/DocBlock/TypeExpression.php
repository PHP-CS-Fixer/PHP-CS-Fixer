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
     * Regex to match any PHP identifier.
     *
     * @internal
     */
    public const REGEX_IDENTIFIER = '(?:(?!(?<!\*)\d)[^\x00-\x2f\x3a-\x40\x5b-\x5e\x60\x7b-\x7f]++)';

    /**
     * Regex to match any PHPDoc type.
     *
     * @internal
     */
    public const REGEX_TYPES = '(?<types>(?x) # one or several types separated by `|` or `&`
'.self::REGEX_TYPE.'
        (?:
            \h*(?<glue>[|&])\h*
            (?&type)
        )*+
    )';

    private const REGEX_TYPE = '(?<type>(?x) # single type
            (?<nullable>\??\h*)
            (?:
                (?<array_shape>
                    (?<array_shape_start>(?i)(?:array|list|object)(?-i)\h*\{\h*)
                    (?<array_shape_inners>
                        (?<array_shape_inner>
                            (?<array_shape_inner_key>(?:(?&constant)|(?&identifier))\h*\??\h*:\h*|)
                            (?<array_shape_inner_value>(?&types_inner))
                        )
                        (?:
                            \h*,\h*
                            (?&array_shape_inner)
                        )*
                        (?:\h*,\h*)?
                    |)
                    \h*\}
                )
                |
                (?<callable> # callable syntax, e.g. `callable(string, int...): bool`
                    (?<callable_start>(?&name)\h*\(\h*)
                    (?<callable_arguments>
                        (?<callable_argument>
                            (?<callable_argument_type>(?&types_inner))
                            (?<callable_argument_is_reference>\h*&|)
                            (?<callable_argument_is_variadic>\h*\.\.\.|)
                            (?<callable_argument_name>\h*\$(?&identifier)|)
                            (?<callable_argument_is_optional>\h*=|)
                        )
                        (?:
                            \h*,\h*
                            (?&callable_argument)
                        )*
                        (?:\h*,\h*)?
                    |)
                    \h*\)
                    (?:
                        \h*\:\h*
                        (?<callable_return>(?&type))
                    )?
                )
                |
                (?<generic> # generic syntax, e.g.: `array<int, \Foo\Bar>`
                    (?<generic_start>(?&name)\h*<\h*)
                    (?<generic_types>
                        (?&types_inner)
                        (?:
                            \h*,\h*
                            (?&types_inner)
                        )*
                        (?:\h*,\h*)?
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
                    # all sorts of numbers: with or without sign, supports literal separator and several numeric systems,
                    # e.g.: 1, +1.1, 1., .1, -1, 123E+8, 123_456_789, 0x7Fb4, 0b0110, 0o777
                    [+-]?(?:
                        (?:0b[01]++(?:_[01]++)*+)
                        | (?:0o[0-7]++(?:_[0-7]++)*+)
                        | (?:0x[\da-f]++(?:_[\da-f]++)*+)
                        | (?:(?<constant_digits>\d++(?:_\d++)*+)|(?=\.\d))
                          (?:\.(?&constant_digits)|(?<=\d)\.)?+
                          (?:e[+-]?(?&constant_digits))?+
                    )
                    | \'(?:[^\'\\\\]|\\\\.)*+\'
                    | "(?:[^"\\\\]|\\\\.)*+"
                    (?-i)
                )
                |
                (?<this> # self reference, e.g.: $this, $self, @static
                    (?i)
                    [@$](?:this | self | static)
                    (?-i)
                )
                |
                (?<name> # full name, e.g.: `int`, `\DateTime`, `\Foo\Bar`, `positive-int`
                    \\\\?+
                    (?<identifier>'.self::REGEX_IDENTIFIER.')
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

    private string $typesGlue = '|';

    /**
     * @var list<array{start_index: int, expression: self}>
     */
    private array $innerTypeExpressions = [];

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

    public function isUnionType(): bool
    {
        return $this->isUnionType;
    }

    public function getTypesGlue(): string
    {
        return $this->typesGlue;
    }

    /**
     * @param \Closure(self): void $callback
     */
    public function walkTypes(\Closure $callback): void
    {
        foreach ($this->innerTypeExpressions as [
            'start_index' => $startIndex,
            'expression' => $inner,
        ]) {
            $initialValueLength = \strlen($inner->toString());

            $inner->walkTypes($callback);

            $this->value = substr_replace(
                $this->value,
                $inner->toString(),
                $startIndex,
                $initialValueLength
            );
        }

        $callback($this);
    }

    /**
     * @param \Closure(self, self): (-1|0|1) $compareCallback
     */
    public function sortTypes(\Closure $compareCallback): void
    {
        $this->walkTypes(static function (self $type) use ($compareCallback): void {
            if ($type->isUnionType) {
                $type->innerTypeExpressions = Utils::stableSort(
                    $type->innerTypeExpressions,
                    static fn (array $type): self => $type['expression'],
                    $compareCallback,
                );

                $type->value = implode($type->getTypesGlue(), $type->getTypes());
            }
        });
    }

    public function getCommonType(): ?string
    {
        $aliases = $this->getAliases();

        $mainType = null;

        foreach ($this->getTypes() as $type) {
            if ('null' === $type) {
                continue;
            }

            if (str_starts_with($type, '?')) {
                $type = substr($type, 1);
            }

            if (Preg::match('/\[\h*\]$/', $type)) {
                $type = 'array';
            } elseif (Preg::match('/^(.+?)\h*[<{(]/', $type, $matches)) {
                $type = $matches[1];
            }

            if (isset($aliases[$type])) {
                $type = $aliases[$type];
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
            if (\in_array($type, ['null', 'mixed'], true) || str_starts_with($type, '?')) {
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
                '{\G'.self::REGEX_TYPE.'(?:\h*(?<glue>[|&])\h*|$)}',
                $this->value,
                $matches,
                PREG_OFFSET_CAPTURE,
                $index
            );

            if ([] === $matches) {
                throw new \Exception('Unable to parse phpdoc type '.var_export($this->value, true));
            }

            if (!$this->isUnionType) {
                if (($matches['glue'][0] ?? '') === '') {
                    break;
                }

                $this->isUnionType = true;
                $this->typesGlue = $matches['glue'][0];
            }

            $this->innerTypeExpressions[] = [
                'start_index' => $index,
                'expression' => $this->inner($matches['type'][0]),
            ];

            $consumedValueLength = \strlen($matches[0][0]);
            $index += $consumedValueLength;

            if (\strlen($this->value) === $index) {
                return;
            }
        }

        $nullableLength = \strlen($matches['nullable'][0]);
        $index = $nullableLength;

        if ('' !== ($matches['generic'][0] ?? '') && $matches['generic'][1] === $nullableLength) {
            $this->parseCommaSeparatedInnerTypes(
                $index + \strlen($matches['generic_start'][0]),
                $matches['generic_types'][0]
            );
        } elseif ('' !== ($matches['callable'][0] ?? '') && $matches['callable'][1] === $nullableLength) {
            $this->parseCallableArgumentTypes(
                $index + \strlen($matches['callable_start'][0]),
                $matches['callable_arguments'][0]
            );

            if ('' !== ($matches['callable_return'][0] ?? '')) {
                $this->innerTypeExpressions[] = [
                    'start_index' => \strlen($this->value) - \strlen($matches['callable_return'][0]),
                    'expression' => $this->inner($matches['callable_return'][0]),
                ];
            }
        } elseif ('' !== ($matches['array_shape'][0] ?? '') && $matches['array_shape'][1] === $nullableLength) {
            $this->parseArrayShapeInnerTypes(
                $index + \strlen($matches['array_shape_start'][0]),
                $matches['array_shape_inners'][0]
            );
        } elseif ('' !== ($matches['parenthesized'][0] ?? '') && $matches['parenthesized'][1] === $nullableLength) {
            $index += \strlen($matches['parenthesized_start'][0]);

            if ('' !== ($matches['conditional'][0] ?? '')) {
                if ('' !== ($matches['conditional_cond_left_types'][0] ?? '')) {
                    $this->innerTypeExpressions[] = [
                        'start_index' => $index,
                        'expression' => $this->inner($matches['conditional_cond_left_types'][0]),
                    ];
                }

                $index += \strlen($matches['conditional_cond_left'][0]) + \strlen($matches['conditional_cond_middle'][0]);

                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['conditional_cond_right_types'][0]),
                ];

                $index += \strlen($matches['conditional_cond_right_types'][0]) + \strlen($matches['conditional_true_start'][0]);

                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['conditional_true_types'][0]),
                ];

                $index += \strlen($matches['conditional_true_types'][0]) + \strlen($matches['conditional_false_start'][0]);

                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['conditional_false_types'][0]),
                ];
            } else {
                $this->innerTypeExpressions[] = [
                    'start_index' => $index,
                    'expression' => $this->inner($matches['parenthesized_types'][0]),
                ];
            }
        }
    }

    private function parseCommaSeparatedInnerTypes(int $startIndex, string $value): void
    {
        $index = 0;
        while (\strlen($value) !== $index) {
            Preg::match(
                '{\G'.self::REGEX_TYPES.'(?:\h*,\h*|$)}',
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

    private function parseCallableArgumentTypes(int $startIndex, string $value): void
    {
        $index = 0;
        while (\strlen($value) !== $index) {
            Preg::match(
                '{\G(?:(?=1)0'.self::REGEX_TYPES.'|(?<_callable_argument>(?&callable_argument))(?:\h*,\h*|$))}',
                $value,
                $prematches,
                0,
                $index
            );
            $consumedValue = $prematches['_callable_argument'];
            $consumedValueLength = \strlen($consumedValue);
            $consumedCommaLength = \strlen($prematches[0]) - $consumedValueLength;

            $addedPrefix = 'Closure(';
            Preg::match(
                '{^'.self::REGEX_TYPES.'$}',
                $addedPrefix.$consumedValue.'): void',
                $matches,
                PREG_OFFSET_CAPTURE
            );

            $this->innerTypeExpressions[] = [
                'start_index' => $startIndex + $index,
                'expression' => $this->inner($matches['callable_argument_type'][0]),
            ];

            $index += $consumedValueLength + $consumedCommaLength;
        }
    }

    private function parseArrayShapeInnerTypes(int $startIndex, string $value): void
    {
        $index = 0;
        while (\strlen($value) !== $index) {
            Preg::match(
                '{\G(?:(?=1)0'.self::REGEX_TYPES.'|(?<_array_shape_inner>(?&array_shape_inner))(?:\h*,\h*|$))}',
                $value,
                $prematches,
                0,
                $index
            );
            $consumedValue = $prematches['_array_shape_inner'];
            $consumedValueLength = \strlen($consumedValue);
            $consumedCommaLength = \strlen($prematches[0]) - $consumedValueLength;

            $addedPrefix = 'array{';
            Preg::match(
                '{^'.self::REGEX_TYPES.'$}',
                $addedPrefix.$consumedValue.'}',
                $matches,
                PREG_OFFSET_CAPTURE
            );

            $this->innerTypeExpressions[] = [
                'start_index' => $startIndex + $index + $matches['array_shape_inner_value'][1] - \strlen($addedPrefix),
                'expression' => $this->inner($matches['array_shape_inner_value'][0]),
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
            'false',
            'float',
            'int',
            'iterable',
            'mixed',
            'never',
            'null',
            'object',
            'resource',
            'string',
            'true',
            'void',
        ], true)) {
            return $type;
        }

        if (Preg::match('/\[\]$/', $type)) {
            return 'array';
        }

        if (Preg::match('/^(.+?)</', $type, $matches)) {
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
            'list' => 'array',
            'real' => 'float',
            'true' => 'bool',
        ];
    }
}
