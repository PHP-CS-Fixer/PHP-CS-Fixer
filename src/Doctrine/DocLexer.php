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

namespace PhpCsFixer\Doctrine;

use PhpCsFixer\Preg;

/**
 * Simple lexer for docblock annotations.
 *
 * @template-extends AbstractLexer<DocLexer::T_*, string>
 */
final class DocLexer extends AbstractLexer
{
    public const T_NONE = 1;
    public const T_INTEGER = 2;
    public const T_STRING = 3;
    public const T_FLOAT = 4;

    // All tokens that are also identifiers should be >= 100
    public const T_IDENTIFIER = 100;
    public const T_AT = 101;
    public const T_CLOSE_CURLY_BRACES = 102;
    public const T_CLOSE_PARENTHESIS = 103;
    public const T_COMMA = 104;
    public const T_EQUALS = 105;
    public const T_FALSE = 106;
    public const T_NAMESPACE_SEPARATOR = 107;
    public const T_OPEN_CURLY_BRACES = 108;
    public const T_OPEN_PARENTHESIS = 109;
    public const T_TRUE = 110;
    public const T_NULL = 111;
    public const T_COLON = 112;
    public const T_MINUS = 113;

    /** @var array<string, self::T*> */
    protected $noCase = [
        '@' => self::T_AT,
        ',' => self::T_COMMA,
        '(' => self::T_OPEN_PARENTHESIS,
        ')' => self::T_CLOSE_PARENTHESIS,
        '{' => self::T_OPEN_CURLY_BRACES,
        '}' => self::T_CLOSE_CURLY_BRACES,
        '=' => self::T_EQUALS,
        ':' => self::T_COLON,
        '-' => self::T_MINUS,
        '\\' => self::T_NAMESPACE_SEPARATOR,
    ];

    /** @var array<string, self::T*> */
    protected $withCase = [
        'true' => self::T_TRUE,
        'false' => self::T_FALSE,
        'null' => self::T_NULL,
    ];

    /**
     * Whether the next token starts immediately, or if there were
     * non-captured symbols before that.
     */
    public function nextTokenIsAdjacent(): bool
    {
        return null === $this->token
            || (null !== $this->lookahead
                && ($this->lookahead->position - $this->token->position) === \strlen($this->token->value));
    }

    protected function getCatchablePatterns(): array
    {
        return [
            '[a-z_\\\][a-z0-9_\:\\\]*[a-z_][a-z0-9_]*',
            '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
            '"(?:""|[^"])*+"',
        ];
    }

    protected function getNonCatchablePatterns(): array
    {
        return ['\s+', '\*+', '(.)'];
    }

    protected function getType(&$value)
    {
        $type = self::T_NONE;

        if ('"' === $value[0]) {
            $value = str_replace('""', '"', substr($value, 1, \strlen($value) - 2));

            return self::T_STRING;
        }

        if (isset($this->noCase[$value])) {
            return $this->noCase[$value];
        }

        if ('_' === $value[0] || '\\' === $value[0] || !Preg::match('/[^A-Za-z]/', $value[0])) {
            return self::T_IDENTIFIER;
        }

        $lowerValue = strtolower($value);

        if (isset($this->withCase[$lowerValue])) {
            return $this->withCase[$lowerValue];
        }

        // Checking numeric value
        if (is_numeric($value)) {
            return str_contains($value, '.') || false !== stripos($value, 'e')
                ? self::T_FLOAT : self::T_INTEGER;
        }

        return $type;
    }
}
