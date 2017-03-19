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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for rules defined in PSR2 ¶2.4.
 *
 * @author Vladimir Boliev <voff.web@gmail.com>
 */
final class MethodChainingIndentationFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * @var string
     */
    private $ident;

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $this->ident = $this->whitespacesConfig->getIndent();
        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            if ($content === '->' && $index > 0) {
                $prev = $tokens[$index - 1];
                $prevContent = $prev->getContent();
                $matches = array();
                if (preg_match('/([\n\r|\n])(\s*)/uis', $prevContent, $matches)) {
                    if (!isset($matches[1]) || !isset($matches[2])) {
                        continue;
                    }
                    $lineBreak = $matches[1];
                    $currentWhitespaces = $matches[2];
                    $prevMeaningIndex = $tokens->getPrevMeaningfulToken($index);
                    $rightWhitespaces = $this->getRightIdents($prevMeaningIndex, $tokens);
                    if ($currentWhitespaces !== $rightWhitespaces) {
                        $prev->setContent($lineBreak.$rightWhitespaces);
                    }
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
            'Method chaining MUST indented with one tab.',
            array(new CodeSample("\$user->setEmail('voff.web@gmail.com')\n    ->setPassword('233434');"),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 50;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * @param int    $index
     * @param Tokens $tokens
     *
     * @return string
     */
    private function getRightIdents($index, Tokens $tokens)
    {
        for ($i = $index; $i >= 0; --$i) {
            if (preg_match('/[\n\r|\n](\s*)/uis', $tokens[$i]->getContent(), $matches)) {
                if ($tokens[$i + 1]->getContent() === '->') {
                    return $matches[1];
                }

                return $matches[1].$this->ident;
            }
        }
    }
}
