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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jean LECORDIER <jean.lecordier@infoclimat.fr>
 */
final class StringConcatenationToInterpolationFixer extends AbstractFixer
{
    public function getName(): string
    {
        return 'Infoclimat/string_concatenation_to_interpolation';
    }

    public function getDefinition(): FixerDefinition
    {
        // Return a definition of the fixer, it will be used in the documentation.
        return new FixerDefinition(
            'Transform a string concatenation by a string interpolation.', // Trailing dot is important. We thrive to use English grammar properly.
            [
                new CodeSample(
                    "<?php \$var = 'var'; \$str = 'string ' . \$var;\n",
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        // Check whether the collection is a candidate for fixing.
        // Has to be ultra cheap to execute.
        return $tokens->isTokenKindFound('.');
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // Add the fixing logic of the fixer here.
        $this->removeEmptyStringConcatenation($file, $tokens);
        $index = 0;
        while ($index < \count($tokens)) {
            $token = $tokens[$index];
            $has_changed = false;
            if (is_variable($token)) {
                $has_changed = $this->handleVariableSomethingCase($tokens, $index);
            } elseif (is_simple_string($token)) {
                $has_changed = $this->handleStringSomethingCase($tokens, $index);
            } elseif (is_interpolation_quote($token)) {
                $has_changed = $this->handleInterpSomethingCase($tokens, $index);
            }
            if (!$has_changed) {
                ++$index;
            }
        }
        $this->removeUselessConcatenation($file, $tokens);
    }

    private function removeEmptyStringConcatenation(\SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = \count($tokens) - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (is_empty_string($token)) {
                $this->removeConcatenationAndEmptyString($tokens, $index);
            } elseif (is_concatenation($token)) {
                $this->removeEmptyStringAndConcatenation($tokens, $index);
            }
        }
    }

    private function removeConcatenationAndEmptyString(Tokens $tokens, int $string_index): void
    {
        $previous_token_index = $tokens->getPrevMeaningfulToken($string_index);
        $previous_token = $tokens[$previous_token_index];
        if (!is_concatenation($previous_token)) {
            return;
        }
        $concat_index = $previous_token_index;
        $previous_previous_token_index = $tokens->getPrevMeaningfulToken($concat_index);
        $tokens->clearRange($previous_previous_token_index + 1, $string_index);
    }

    private function removeEmptyStringAndConcatenation(Tokens $tokens, int $concat_index): void
    {
        $previous_token_index = $tokens->getPrevMeaningfulToken($concat_index);
        $previous_token = $tokens[$previous_token_index];
        if (!is_empty_string($previous_token)) {
            return;
        }
        $string_index = $previous_token_index;
        $next_token_index = $tokens->getNextMeaningfulToken($concat_index);

        $tokens->clearRange($string_index, $next_token_index - 1);
    }

    private function removeUselessConcatenation(\SplFileInfo $file, Tokens $tokens): void
    {
        $concat_fixer = new NoUselessConcatOperatorFixer();
        $concat_fixer->configure(['juggle_simple_strings' => true]);
        $concat_fixer->applyFix($file, $tokens);
    }

    /**
     * Transforms `$var . ' string'` to `"{$var} string"`
     * or `$var . " {$interp}"` to `"{$var} {$interp}"`
     * or `$var . $var` to `"{$var}{$var}"`.
     */
    private function handleVariableSomethingCase(Tokens $tokens, int $variable_index): bool
    {
        $variable_end_index = get_variable_end_index($tokens, $variable_index);

        $next_token_index = $tokens->getNextMeaningfulToken($variable_end_index);
        $next_token = $tokens[$next_token_index];
        if (!is_concatenation($next_token)) {
            return false;
        }
        $concat_index = $next_token_index;

        $next_next_token_index = $tokens->getNextMeaningfulToken($concat_index);
        $next_next_token = $tokens[$next_next_token_index];

        // Transforms `$var . ' string'` to `"{$var} string"`
        if (is_simple_string($next_next_token)) {
            return $this->handleVariableStringCase(
                $tokens,
                $variable_index,
                $variable_end_index,
                $next_next_token_index
            );
        }

        // Transforms `$var . " {$interp}"` to `"{$var} {$interp}"`
        if (is_interpolation_quote($next_next_token)) {
            return $this->handleVariableInterpCase(
                $tokens,
                $variable_index,
                $variable_end_index,
                $next_next_token_index
            );
        }

        // Transforms `$var . $var` to `"{$var}{$var}"`
        if (is_variable($next_next_token)) {
            return $this->handleVariableVariableCase(
                $tokens,
                $variable_index,
                $variable_end_index,
                $next_next_token_index
            );
        }

        return false;
    }

    /**
     * Transforms `$var . ' string'` to `"{$var} string"`.
     */
    private function handleVariableStringCase(
        Tokens $tokens,
        int $variable_index,
        int $variable_end_index,
        int $right_string_index
    ): bool {
        $right_string_token = $tokens[$right_string_index];

        // The closing part must be done first, because the opening part will change the indexes
        // Replaces `$var . ' string'` by `$var} string"`
        $tokens->overrideRange(
            $variable_end_index + 1,
            $right_string_index,
            [
                get_close_curly_token(),
                get_raw_string_token($right_string_token),
                get_close_interpolation_token(),
            ]
        );

        // Replaces `$var` by `"{$var`
        $tokens->insertAt(
            $variable_index,
            [
                get_open_interpolation_token(),
                get_open_curly_token(),
            ]
        );

        return true;
    }

    /**
     * Transforms `$var . " {$interp}"` to `"{$var} {$interp}"`.
     */
    private function handleVariableInterpCase(
        Tokens $tokens,
        int $variable_index,
        int $variable_end_index,
        int $open_quote_index
    ): bool {
        // The closing part must be done first, because the opening part will change the indexes
        // Replaces `$var . "` by `$var}`
        $tokens->overrideRange(
            $variable_end_index + 1,
            $open_quote_index,
            [get_close_curly_token()]
        );

        // Replaces `$var . "` by `"{$var`
        $tokens->insertAt(
            $variable_index,
            [
                get_open_interpolation_token(),
                get_open_curly_token(),
            ]
        );

        return true;
    }

    /**
     * Transforms `$var . $var` to `"{$var}{$var}"`.
     */
    private function handleVariableVariableCase(
        Tokens $tokens,
        int $left_variable_index,
        int $left_variable_end_index,
        int $right_variable_index
    ): bool {
        $right_variable_end_index = get_variable_end_index($tokens, $right_variable_index);

        // The closing part must be done first, because the opening part will change the indexes
        // Replaces `$var` by `$var}"`
        $tokens->insertAt(
            $right_variable_end_index + 1,
            [
                get_close_curly_token(),
                get_close_interpolation_token(),
            ]
        );

        // Replaces `$var . $var` by `$var}{$var`
        $tokens->overrideRange(
            $left_variable_end_index + 1,
            $right_variable_index - 1,
            [
                get_close_curly_token(),
                get_open_curly_token(),
            ]
        );

        // Replaces `$var` by `"{$var`
        $tokens->insertAt(
            $left_variable_index,
            [
                get_open_interpolation_token(),
                get_open_curly_token(),
            ]
        );

        return true;
    }

    /**
     * Transforms `'string ' . $var` to `"string {$var}"`
     * or `'string ' . "{$interp}"` to `"string {$interp}"`.
     */
    private function handleStringSomethingCase(Tokens $tokens, int $left_string_index): bool
    {
        $next_token_index = $tokens->getNextMeaningfulToken($left_string_index);
        $next_token = $tokens[$next_token_index];
        if (!is_concatenation($next_token)) {
            return false;
        }
        $concat_index = $next_token_index;

        $next_next_token_index = $tokens->getNextMeaningfulToken($concat_index);
        $next_next_token = $tokens[$next_next_token_index];

        // Transforms `'string ' . $var` to `"string {$var}"`
        if (is_variable($next_next_token)) {
            return $this->handleStringVariableCase(
                $tokens,
                $left_string_index,
                $next_next_token_index
            );
        }

        // Transforms `'string ' . "{$interp}"` to `"string {$interp}"`
        if (is_interpolation_quote($next_next_token)) {
            return $this->handleStringInterpCase(
                $tokens,
                $left_string_index,
                $next_next_token_index
            );
        }

        return false;
    }

    /**
     * Transforms `'string ' . $var` to `"string {$var}"`.
     */
    private function handleStringVariableCase(
        Tokens $tokens,
        int $left_string_index,
        int $variable_index
    ): bool {
        $left_string_token = $tokens[$left_string_index];
        $variable_end_index = get_variable_end_index($tokens, $variable_index);

        // The closing part must be done first, because the opening part will change the indexes
        // Replaces `$var` by `$var}"`
        $tokens->insertAt(
            $variable_end_index + 1,
            [
                get_close_curly_token(),
                get_close_interpolation_token(),
            ]
        );

        // Replaces `'string ' . ` by `"string {`
        $tokens->overrideRange(
            $left_string_index,
            $variable_index - 1,
            [
                get_open_interpolation_token(),
                get_raw_string_token($left_string_token),
                get_open_curly_token(),
            ]
        );

        return true;
    }

    /**
     * Transforms `'string ' . "{$interp}"` to `"string {$interp}"`.
     */
    private function handleStringInterpCase(
        Tokens $tokens,
        int $left_string_index,
        int $open_quote_index
    ): bool {
        $left_string_token = $tokens[$left_string_index];

        // Replaces `'string ' . "` by `"string `
        $tokens->overrideRange(
            $left_string_index,
            $open_quote_index,
            [
                get_open_interpolation_token(),
                get_raw_string_token($left_string_token),
            ]
        );

        return true;
    }

    /**
     * Transforms `"{$interp} " . $var` to `"{$interp} {$var}"`
     * or `"{$interp} " . 'string'` to `"{$interp} string"`.
     */
    private function handleInterpSomethingCase(Tokens $tokens, int $open_quote_index): bool
    {
        $close_quote_token_index = get_closing_quote_index($tokens, $open_quote_index);
        if (null === $close_quote_token_index) {
            return false;
        }

        $next_token_index = $tokens->getNextMeaningfulToken($close_quote_token_index);
        $next_token = $tokens[$next_token_index];
        if (!is_concatenation($next_token)) {
            return false;
        }
        $concat_token_index = $next_token_index;

        $next_next_token_index = $tokens->getNextMeaningfulToken($concat_token_index);
        $next_next_token = $tokens[$next_next_token_index];

        // Transforms `"{$interp} " . $var` to `"{$interp} {$var}"`
        if (is_variable($next_next_token)) {
            return $this->handleInterpVariableCase(
                $tokens,
                $close_quote_token_index,
                $next_next_token_index
            );
        }

        // Transforms `"{$interp} " . 'string'` to `"{$interp} string"`
        if (is_simple_string($next_next_token)) {
            return $this->handleInterpStringCase(
                $tokens,
                $close_quote_token_index,
                $next_next_token_index
            );
        }

        return false;
    }

    /**
     * Transforms `"{$interp} " . $var` to `"{$interp} {$var}"`.
     */
    private function handleInterpVariableCase(
        Tokens $tokens,
        int $close_quote_token_index,
        int $variable_index
    ): bool {
        $variable_end_index = get_variable_end_index($tokens, $variable_index);

        // The closing part must be done first, because the opening part will change the indexes
        // Replaces `$var` by `$var}"`
        $tokens->insertAt(
            $variable_end_index + 1,
            [
                get_close_curly_token(),
                get_close_interpolation_token(),
            ]
        );

        // Replaces `" . $var` by `{$var`
        $tokens->overrideRange(
            $close_quote_token_index,
            $variable_index - 1,
            [get_open_curly_token()]
        );

        return true;
    }

    /**
     * Transforms `"{$interp} " . 'string"` to `"{$interp} string"`.
     */
    private function handleInterpStringCase(
        Tokens $tokens,
        int $close_quote_token_index,
        int $right_string_index
    ): bool {
        $right_string_token = $tokens[$right_string_index];

        // Replaces `" . 'string'` by `string"`
        $tokens->overrideRange(
            $close_quote_token_index,
            $right_string_index,
            [
                get_raw_string_token($right_string_token),
                get_close_interpolation_token(),
            ]
        );

        return true;
    }
}

function is_concatenation(Token $token): bool
{
    return '.' === $token->getContent();
}

function is_variable(Token $token): bool
{
    return $token->isGivenKind(T_VARIABLE);
}

function is_simple_string(Token $token): bool
{
    return $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING);
}

function is_empty_string(Token $token): bool
{
    return "''" === $token->getContent() || '""' === $token->getContent();
}

function is_interpolation_quote(Token $token): bool
{
    return '"' === $token->getContent();
}

function is_array_access_opening(Token $token): bool
{
    return '[' === $token->getContent();
}

function is_object_access_opening(Token $token): bool
{
    return $token->isGivenKind($token->getObjectOperatorKinds());
}

/**
 * Transforms `'string'` to `string`.
 */
function get_T_CONSTANT_ENCAPSED_STRING_content(Token $token): string
{
    return substr($token->getContent(), 1, -1);
}

/**
 * Transforms `'string'` to `string`.
 */
function get_raw_string_token(Token $token): Token
{
    return new Token([
        T_ENCAPSED_AND_WHITESPACE,
        get_T_CONSTANT_ENCAPSED_STRING_content($token),
    ]);
}

function get_open_curly_token(): Token
{
    return new Token([T_CURLY_OPEN, '{']);
}

function get_close_curly_token(): Token
{
    return new Token([CT::T_CURLY_CLOSE, '}']);
}

function enclose_variable_token(Token $variable_token): array
{
    return [
        get_open_curly_token(),
        $variable_token,
        get_close_curly_token(),
    ];
}

function get_interpolation_boundary_token(): Token
{
    return new Token('"');
}

function get_open_interpolation_token(): Token
{
    return get_interpolation_boundary_token();
}

function get_close_interpolation_token(): Token
{
    return get_interpolation_boundary_token();
}

function quote_interpolation(array $tokens): array
{
    return [
        get_open_interpolation_token(),
        ...$tokens,
        get_close_interpolation_token(),
    ];
}

function get_closing_quote_index(Tokens $tokens, int $open_quote_index): ?int
{
    return $tokens->getNextTokenOfKind($open_quote_index, ['"']);
}

function get_closing_array_access_index(Tokens $tokens, int $array_access_opening_index): int
{
    return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $array_access_opening_index);
}

function is_method_opening(Token $token): bool
{
    return '(' === $token->getContent();
}

function get_closing_object_access_index(Tokens $tokens, int $object_access_opening_index): int
{
    $prop_or_method_index = $tokens->getNextMeaningfulToken($object_access_opening_index);
    $next_next_token_index = $tokens->getNextMeaningfulToken($prop_or_method_index);
    $next_next_token = $tokens[$next_next_token_index];
    if (!is_method_opening($next_next_token)) {
        return $prop_or_method_index;
    }
    $method_opening_index = $next_next_token_index;

    return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $method_opening_index);
}

function get_variable_end_index(Tokens $tokens, int $variable_start_index): int
{
    $variable_end_index = $variable_start_index;
    $next_token_index = $tokens->getNextMeaningfulToken($variable_end_index);
    $next_token = $tokens[$next_token_index];
    if (is_array_access_opening($next_token)) {
        $array_access_opening_index = $next_token_index;
        $variable_end_index = get_closing_array_access_index($tokens, $array_access_opening_index);

        return get_variable_end_index($tokens, $variable_end_index);
    }
    if (is_object_access_opening($next_token)) {
        $object_access_opening_index = $next_token_index;
        $variable_end_index = get_closing_object_access_index($tokens, $object_access_opening_index);

        return get_variable_end_index($tokens, $variable_end_index);
    }

    return $variable_end_index;
}
