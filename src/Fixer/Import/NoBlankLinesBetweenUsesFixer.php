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
 * @author Luis Cordova <cordoval@gmail.com>
 */
final class NoBlankLinesBetweenUsesFixer extends AbstractFixer
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

        $namespacesImports = $tokensAnalyzer->getImportUseIndexes(true);

        if (!count($namespacesImports)) {
            return;
        }

        foreach ($namespacesImports as $uses) {
            $uses = array_reverse($uses);
            $this->fixLineBreaksPerImportGroup($tokens, $uses);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before OrderedImportsFixer
        return -5;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Removes line breaks between use statements.';
    }

    /**
     * Fix the line breaks per group.
     *
     * For each use token reach the nearest ; and ensure every
     * token after has one \n before next non empty token (next line).
     * It skips the first pass from the bottom.
     *
     * @param Tokens $tokens
     * @param array  $uses
     */
    private function fixLineBreaksPerImportGroup(Tokens $tokens, array $uses)
    {
        foreach ($uses as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, array(';'));
            $afterSemicolonIndex = $tokens->getNextNonWhitespace($endIndex);

            if (null !== $afterSemicolonIndex && !$tokens[$afterSemicolonIndex]->isGivenKind(T_USE)) {
                continue;
            }

            $nextToken = $tokens[$endIndex + 1];
            if ($nextToken->isWhitespace()) {
                $nextToken->setContent(preg_replace('/\n{2,}/', "\n", $nextToken->getContent()));
            }
        }
    }
}
