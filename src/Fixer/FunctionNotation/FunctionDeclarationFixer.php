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

namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 generally (¶1 and ¶6).
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FunctionDeclarationFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    const SPACING_NONE = 'none';

    /**
     * @internal
     */
    const SPACING_ONE = 'one';

    private $supportedSpacings = array(self::SPACING_NONE, self::SPACING_ONE);

    private $singleLineWhitespaceOptions = " \t";

    /**
     * @var array
     */
    private static $defaultConfiguration = array(
        'closure_function_spacing' => self::SPACING_ONE,
    );

    /**
     * @param array|null $configuration
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            $this->configuration = self::$defaultConfiguration;

            return;
        }

        foreach ($configuration as $key => $value) {
            if (!array_key_exists($key, self::$defaultConfiguration)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf('"%s" is not handled by the fixer.', $key));
            }

            if ('closure_function_spacing' === $key && !in_array($value, $this->supportedSpacings, true)) {
                throw new InvalidFixerConfigurationException(
                    $this->getName(),
                    sprintf('Spacing is invalid. Should be one of: "%s".', implode('", "', $this->supportedSpacings))
                );
            }
        }

        $this->configuration = array_merge(self::$defaultConfiguration, $configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, array('(', ';', array(T_CLOSE_TAG)));
            if (!$tokens[$startParenthesisIndex]->equals('(')) {
                continue;
            }

            $endParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $startBraceIndex = $tokens->getNextTokenOfKind($endParenthesisIndex, array(';', '{'));

            // fix single-line whitespace before {
            // eg: `function foo(){}` => `function foo() {}`
            // eg: `function foo()   {}` => `function foo() {}`
            if (
                $tokens[$startBraceIndex]->equals('{') &&
                (
                    !$tokens[$startBraceIndex - 1]->isWhitespace() ||
                    $tokens[$startBraceIndex - 1]->isWhitespace($this->singleLineWhitespaceOptions)
                )
            ) {
                $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
            }

            $afterParenthesisIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
            $afterParenthesisToken = $tokens[$afterParenthesisIndex];

            if ($afterParenthesisToken->isGivenKind(CT::T_USE_LAMBDA)) {
                // fix whitespace after CT:T_USE_LAMBDA (we might add a token, so do this before determining start and end parenthesis)
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex + 1, 0, ' ');

                $useStartParenthesisIndex = $tokens->getNextTokenOfKind($afterParenthesisIndex, array('('));
                $useEndParenthesisIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $useStartParenthesisIndex);

                // remove single-line edge whitespaces inside use parentheses
                $this->fixParenthesisInnerEdge($tokens, $useStartParenthesisIndex, $useEndParenthesisIndex);

                // fix whitespace before CT::T_USE_LAMBDA
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex - 1, 1, ' ');
            }

            // remove single-line edge whitespaces inside parameters list parentheses
            $this->fixParenthesisInnerEdge($tokens, $startParenthesisIndex, $endParenthesisIndex);

            $isLambda = $tokensAnalyzer->isLambda($index);

            // remove whitespace before (
            // eg: `function foo () {}` => `function foo() {}`
            if (!$isLambda && $tokens[$startParenthesisIndex - 1]->isWhitespace()) {
                $tokens[$startParenthesisIndex - 1]->clear();
            }

            if ($isLambda && self::SPACING_NONE === $this->configuration['closure_function_spacing']) {
                // optionally remove whitespace after T_FUNCTION of a closure
                // eg: `function () {}` => `function() {}`
                if ($tokens[$index + 1]->isWhitespace()) {
                    $tokens[$index + 1]->clear();
                }
            } else {
                // otherwise, enforce whitespace after T_FUNCTION
                // eg: `function     foo() {}` => `function foo() {}`
                $tokens->ensureWhitespaceAtIndex($index + 1, 0, ' ');
            }

            if ($isLambda) {
                $prev = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prev]->isGivenKind(T_STATIC)) {
                    // fix whitespace after T_STATIC
                    // eg: `$a = static     function(){};` => `$a = static function(){};`
                    $tokens->ensureWhitespaceAtIndex($prev + 1, 0, ' ');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Spaces should be properly placed in a function declaration.',
            array(
                new CodeSample(
'<?php

function  foo  ($bar, $baz)
{
    return false;
}
'
                ),
                new CodeSample(
'<?php

class Foo
{
    public static function  bar ($baz)
    {
        return false;
    }
}
'
                ),
                new CodeSample(
'<?php
$f = function () {};
',
                    array('closure_function_spacing' => self::SPACING_NONE)
                ),
            ),
            null,
            'The `closure_function_spacing` key configures whether there should be a space character after the `function` keyword of an anonymous function; it can be either "none" or "one".',
            self::$defaultConfiguration
        );
    }

    private function fixParenthesisInnerEdge(Tokens $tokens, $start, $end)
    {
        // remove single-line whitespace before )
        if ($tokens[$end - 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens[$end - 1]->clear();
        }

        // remove single-line whitespace after (
        if ($tokens[$start + 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens[$start + 1]->clear();
        }
    }
}
