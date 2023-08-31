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
use UnitEnum;

/**
 * Base class for writing simple lexers, i.e. for creating small DSLs.
 *
 * @template T of UnitEnum|string|int
 * @template V of string|int
 */
abstract class AbstractLexer
{
    /**
     * The next token in the input.
     *
     * @var null|mixed[]
     */
    public $lookahead;

    /**
     * The last matched/seen token.
     *
     * @var null|mixed[]
     */
    public $token;

    /**
     * Lexer original input string.
     *
     * @var string
     */
    private $input;

    /**
     * Array of scanned tokens.
     *
     * @var list<Token<T, V>>
     */
    private $tokens = [];

    /**
     * Current lexer position in input string.
     *
     * @var int
     */
    private $position = 0;

    /**
     * Current peek of current lexer position.
     *
     * @var int
     */
    private $peek = 0;

    /**
     * Composed regex for input parsing.
     *
     * @var null|string
     */
    private $regex;

    /**
     * Sets the input data to be tokenized.
     *
     * The Lexer is immediately reset and the new input tokenized.
     * Any unprocessed tokens from any previous input are lost.
     *
     * @param string $input the input to be tokenized
     */
    public function setInput(string $input): void
    {
        $this->input = $input;
        $this->tokens = [];

        $this->reset();
        $this->scan($input);
    }

    /**
     * Resets the lexer.
     */
    public function reset(): void
    {
        $this->lookahead = null;
        $this->token = null;
        $this->peek = 0;
        $this->position = 0;
    }

    /**
     * Resets the peek pointer to 0.
     */
    public function resetPeek(): void
    {
        $this->peek = 0;
    }

    /**
     * Resets the lexer position on the input to the given position.
     *
     * @param int $position position to place the lexical scanner
     */
    public function resetPosition(int $position = 0): void
    {
        $this->position = $position;
    }

    /**
     * Retrieve the original lexer's input until a given position.
     */
    public function getInputUntilPosition(int $position): string
    {
        return substr($this->input, 0, $position);
    }

    /**
     * Checks whether a given token matches the current lookahead.
     *
     * @param mixed $type
     */
    public function isNextToken($type): bool
    {
        return null !== $this->lookahead && $this->lookahead->isA($type);
    }

    /**
     * Checks whether any of the given tokens matches the current lookahead.
     *
     * @param list<T> $types
     */
    public function isNextTokenAny(array $types): bool
    {
        return null !== $this->lookahead && $this->lookahead->isA(...$types);
    }

    /**
     * Moves to the next token in the input string.
     */
    public function moveNext(): bool
    {
        $this->peek = 0;
        $this->token = $this->lookahead;
        $this->lookahead = isset($this->tokens[$this->position])
            ? $this->tokens[$this->position++] : null;

        return null !== $this->lookahead;
    }

    /**
     * Tells the lexer to skip input tokens until it sees a token with the given value.
     *
     * @param mixed $type
     */
    public function skipUntil($type): void
    {
        while (null !== $this->lookahead && !$this->lookahead->isA($type)) {
            $this->moveNext();
        }
    }

    /**
     * Checks if given value is identical to the given token.
     *
     * @param int|string $token
     */
    public function isA(string $value, $token): bool
    {
        return $this->getType($value) === $token;
    }

    /**
     * Moves the lookahead token forward.
     *
     * @return null|Token the next token or NULL if there are no more tokens ahead
     */
    public function peek(): ?Token
    {
        if (isset($this->tokens[$this->position + $this->peek])) {
            return $this->tokens[$this->position + $this->peek++];
        }

        return null;
    }

    /**
     * Peeks at the next token, returns it and immediately resets the peek.
     *
     * @return null|Token the next token or NULL if there are no more tokens ahead
     */
    public function glimpse(): ?Token
    {
        $peek = $this->peek();
        $this->peek = 0;

        return $peek;
    }

    /**
     * Gets the literal for a given token.
     *
     * @param mixed $token
     *
     * @return int|string
     */
    public function getLiteral($token)
    {
        if ($token instanceof \UnitEnum) {
            return \get_class($token).'::'.$token->name;
        }

        $className = static::class;

        $reflClass = new \ReflectionClass($className);
        $constants = $reflClass->getConstants();

        foreach ($constants as $name => $value) {
            if ($value === $token) {
                return $className.'::'.$name;
            }
        }

        return $token;
    }

    /**
     * Scans the input string for tokens.
     *
     * @param string $input a query string
     */
    protected function scan(string $input): void
    {
        if (!isset($this->regex)) {
            $this->regex = sprintf(
                '/(%s)|%s/%s',
                implode(')|(', $this->getCatchablePatterns()),
                implode('|', $this->getNonCatchablePatterns()),
                $this->getModifiers()
            );
        }

        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = Preg::split($this->regex, $input, -1, $flags);

        if (false === $matches) {
            // Work around https://bugs.php.net/78122
            $matches = [[$input, 0]];
        }

        foreach ($matches as $match) {
            // Must remain before 'value' assignment since it can change content
            $firstMatch = $match[0];
            $type = $this->getType($firstMatch);

            $this->tokens[] = new Token(
                $firstMatch,
                $type,
                $match[1]
            );
        }
    }

    protected function getModifiers(): string
    {
        return 'iu';
    }

    /**
     * @return string[]
     */
    abstract protected function getCatchablePatterns(): array;

    /**
     * @return string[]
     */
    abstract protected function getNonCatchablePatterns(): array;

    /**
     * Retrieve token type. Also processes the token value if necessary.
     */
    abstract protected function getType(string &$value);
}
