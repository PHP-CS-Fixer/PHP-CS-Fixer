<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
final class NoLeadingImportSlashFixer extends AbstractFixer
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
        $foundNamespace = $tokens->findGivenKind(T_NAMESPACE);
        if (empty($foundNamespace)) {
            return;
        }

        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $firstNamespaceIdx = key($foundNamespace);

        $usesIdxs = $tokensAnalyzer->getImportUseIndexes();

        foreach ($usesIdxs as $idx) {
            if ($idx < $firstNamespaceIdx) {
                continue;
            }

            $nextTokenIdx = $tokens->getNextNonWhitespace($idx);
            $nextToken = $tokens[$nextTokenIdx];

            if ($nextToken->isGivenKind(T_NS_SEPARATOR)) {
                $nextToken->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the SingleImportPerStatementFixer (for fix separated use statements as well) and NoUnusedImportsFixer (just for save performance)
        return -20;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove leading slashes in use clauses.';
    }
}
