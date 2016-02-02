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

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum <possumfromspace@gmail.com>
 */
final class NoExtraConsecutiveBlankLinesFixer extends AbstractFixer
{
    /**
     * @var array<int, string> key is token id, value is name of callback
     */
    private $tokenCallbackMap = array(T_WHITESPACE => 'removeMultipleBlankLines');

    /**
     * @var Tokens
     */
    private $tokens;

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
     *
     * @param string[]|null $configuration
     */
    public function configure(array $configuration = null)
    {
        if (null === $configuration) {
            return;
        }

        $this->tokenCallbackMap = array();
        foreach ($configuration as $item) {
            switch ($item) {
                case 'break':
                    $this->tokenCallbackMap[T_BREAK] = 'removeEmptyLinesAfterLineWithTokenAt';
                    break;
                case 'continue':
                    $this->tokenCallbackMap[T_CONTINUE] = 'removeEmptyLinesAfterLineWithTokenAt';
                    break;
                case 'extra':
                    $this->tokenCallbackMap[T_WHITESPACE] = 'removeMultipleBlankLines';
                    break;
                case 'return':
                    $this->tokenCallbackMap[T_RETURN] = 'removeEmptyLinesAfterLineWithTokenAt';
                    break;
                case 'throw':
                    $this->tokenCallbackMap[T_THROW] = 'removeEmptyLinesAfterLineWithTokenAt';
                    break;
                case 'use':
                    $this->tokenCallbackMap[T_USE] = 'removeBetweenUse';
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
        // should be run after the NoUnusedImportsFixer, NoEmptyPhpdocFixer.
        return -20;
    }

    private function fixByToken(Token $token, $index)
    {
        foreach ($this->tokenCallbackMap as $kind => $callback) {
            if (!$token->isGivenKind($kind)) {
                continue;
            }

            $callback = $this->tokenCallbackMap[$kind];
            $this->$callback($index);

            return;
        }
    }

    private function removeBetweenUse($index)
    {
        $tokenCount = count($this->tokens);
        for ($i = $index; $i < $tokenCount; ++$i) {
            if (!$this->tokens[$i]->equals(';')) {
                continue;
            }

            $next = $this->tokens->getNextMeaningfulToken($i);
            if (!$this->tokens[$next]->isGivenKind(T_USE)) {
                continue;
            }

            for ($i = $next; $i > $index; --$i) {
                if ($this->tokens[$i]->isWhitespace() && substr_count($this->tokens[$i]->getContent(), "\n") > 1) {
                    $this->tokens[$i]->setContent("\n");
                }
            }
            break;
        }
    }

    private function removeMultipleBlankLines($index)
    {
        $token = $this->tokens[$index];
        $content = '';
        $count = 0;
        $parts = explode("\n", $token->getContent());

        for ($i = 0, $last = count($parts) - 1; $i <= $last; ++$i) {
            if ('' === $parts[$i] || "\r" === $parts[$i]) {
                // if part is empty then we between two \n
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
