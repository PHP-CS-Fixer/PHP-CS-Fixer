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

namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Transformers;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum <possumfromspace@gmail.com>
 */
final class NoExtraConsecutiveBlankLinesFixer extends AbstractFixer
{
    /**
     * @var array<int, string> key is token id, value is name of callback
     */
    private $tokenKindCallbackMap = array(T_WHITESPACE => 'removeMultipleBlankLines');

    /**
     * @var array<string, string> token prototype, value is name of callback
     */
    private $tokenEqualsMap = array();

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;

    /**
     * Set configuration.
     *
     * Valid configuration options are:
     * - 'break' remove blank lines after a line with a 'break' statement
     * - 'continue' remove blank lines after a line with a 'continue' statement
     * - 'extra' [default] consecutive blank lines are squashed into one
     * - 'return' remove blank lines after a line with a 'return' statement
     * - 'throw' remove blank lines after a line with a 'throw' statement
     * - 'use' remove blank lines between 'use' import statements
     * - 'useTrait' remove blank lines between 'use' trait statements
     * - 'curly_brace_open' remove blank lines after a curly opening brace ('{')
     *
     * @param string[]|null $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            return;
        }

        if (!defined('CT_USE_TRAIT')) {
            Transformers::create(); // TODO could use a better fix
        }

        $this->tokenKindCallbackMap = array();
        $this->tokenEqualsMap = array();
        foreach ($configuration as $item) {
            switch ($item) {
                case 'break':
                    $this->tokenKindCallbackMap[T_BREAK] = 'fixAfterToken';
                    break;
                case 'continue':
                    $this->tokenKindCallbackMap[T_CONTINUE] = 'fixAfterToken';
                    break;
                case 'extra':
                    $this->tokenKindCallbackMap[T_WHITESPACE] = 'removeMultipleBlankLines';
                    break;
                case 'return':
                    $this->tokenKindCallbackMap[T_RETURN] = 'fixAfterToken';
                    break;
                case 'throw':
                    $this->tokenKindCallbackMap[T_THROW] = 'fixAfterToken';
                    break;
                case 'use':
                    $this->tokenKindCallbackMap[T_USE] = 'removeBetweenUse';
                    break;
                case 'useTrait':
                    $this->tokenKindCallbackMap[CT_USE_TRAIT] = 'removeBetweenUse';
                    break;
                case 'curly_brace_open':
                    $this->tokenEqualsMap['{'] = 'fixAfterToken';
                    break;
                default:
                    throw new InvalidFixerConfigurationException($this->getName(), sprintf('Unknown configuration item "%s" passed.', $item));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_WHITESPACE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->tokens = $tokens;
        $this->tokensAnalyzer = new TokensAnalyzer($this->tokens);
        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $this->fixByToken($tokens[$index], $index);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes extra blank lines and/or blank lines following configuration.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the NoUnusedImportsFixer, NoEmptyPhpdocFixer, CombineConsecutiveUnsetsFixer and NoUselessElseFixer
        return -20;
    }

    private function fixByToken(Token $token, $index)
    {
        foreach ($this->tokenKindCallbackMap as $kind => $callback) {
            if (!$token->isGivenKind($kind)) {
                continue;
            }

            $this->$callback($index);

            return;
        }

        foreach ($this->tokenEqualsMap as $equals => $callback) {
            if (!$token->equals($equals)) {
                continue;
            }

            $this->$callback($index);
        }
    }

    private function removeBetweenUse($index)
    {
        $next = $this->tokens->getNextTokenOfKind($index, array(';', T_CLOSE_TAG));
        if (null === $next || $this->tokens[$next]->isGivenKind(T_CLOSE_TAG)) {
            return;
        }

        $nextUseCandidate = $this->tokens->getNextMeaningfulToken($next);
        if (null === $nextUseCandidate || 1 === $nextUseCandidate - $next || !$this->tokens[$nextUseCandidate]->isGivenKind($this->tokens[$index]->getId())) {
            return;
        }

        return $this->removeEmptyLinesAfterLineWithTokenAt($next);
    }

    private function removeMultipleBlankLines($index)
    {
        $token = $this->tokens[$index];
        $content = '';
        $count = 0;
        $parts = explode("\n", $token->getContent());

        for ($i = 0, $last = count($parts) - 1; $i <= $last; ++$i) {
            if ('' === $parts[$i] || "\r" === $parts[$i]) {
                // if part is empty then we are between two "\n"
                ++$count;
            } else {
                $content .= $parts[$i];
            }

            if ($i !== $last && $count < 3) {
                $content .= "\n";
            }
        }

        $token->setContent($content);
    }

    private function fixAfterToken($index)
    {
        for ($i = $index - 1; $i > 0; --$i) {
            if ($this->tokens[$i]->isGivenKind(T_FUNCTION) && $this->tokensAnalyzer->isLambda($i)) {
                return;
            }

            if ($this->tokens[$i]->isGivenKind(T_CLASS) && $this->tokensAnalyzer->isAnonymousClass($i)) {
                return;
            }

            if ($this->tokens[$i]->isWhitespace() && false !== strpos($this->tokens[$i]->getContent(), "\n")) {
                break;
            }
        }

        $this->removeEmptyLinesAfterLineWithTokenAt($index);
    }

    private function removeEmptyLinesAfterLineWithTokenAt($index)
    {
        // find the line break
        $tokenCount = count($this->tokens);
        for ($end = $index; $end < $tokenCount; ++$end) {
            if (false !== strpos($this->tokens[$end]->getContent(), "\n")) {
                break;
            }
        }

        if ($end === $tokenCount) {
            return; // not found, early return
        }

        for ($i = $end; $i < $tokenCount && $this->tokens[$i]->isWhitespace(); ++$i) {
            $content = $this->tokens[$i]->getContent();
            if (substr_count($content, "\n") < 1) {
                continue;
            }

            $pos = strrpos($content, "\n");
            if ($pos + 2 < strlen($content)) { // preserve indenting where possible
                $this->tokens[$i]->setContent("\n".substr($content, $pos + 1));
            } else {
                $this->tokens[$i]->setContent("\n");
            }
        }
    }
}
