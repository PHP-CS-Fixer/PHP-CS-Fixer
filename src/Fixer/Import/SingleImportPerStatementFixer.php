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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleImportPerStatementFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_USE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $uses = array_reverse($tokensAnalyzer->getImportUseIndexes());

        foreach ($uses as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, array(';'));
            $declarationContent = $tokens->generatePartialCode($index + 1, $endIndex - 1);

            $declarationParts = explode(',', $declarationContent);

            if (1 === count($declarationParts)) {
                continue;
            }

            $declarationContent = array();

            foreach ($declarationParts as $declarationPart) {
                $declarationContent[] = 'use '.trim($declarationPart).';';
            }

            $declarationContent = implode("\n".$this->detectIndent($tokens, $index), $declarationContent);

            for ($i = $index; $i <= $endIndex; ++$i) {
                $tokens[$i]->clear();
            }

            $declarationTokens = Tokens::fromCode('<?php '.$declarationContent);
            $declarationTokens[0]->clear();
            $declarationTokens->clearEmptyTokens();

            $tokens->insertAt($index, $declarationTokens);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'There MUST be one use keyword per declaration.';
    }

    private function detectIndent(Tokens $tokens, $index)
    {
        $prevIndex = $index - 1;
        $prevToken = $tokens[$prevIndex];

        // if can not detect indent:
        if (!$prevToken->isWhitespace()) {
            return '';
        }

        $explodedContent = explode("\n", $prevToken->getContent());

        return end($explodedContent);
    }
}
