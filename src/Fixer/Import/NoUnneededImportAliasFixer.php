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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

final class NoUnneededImportAliasFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Remove unneeded alias in `use` clauses.',
            [new CodeSample("<?php\nnamespace Foo;\nuse Bar\\Baz as Baz;\n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the NoUnusedImportsFixer (just for save performance)
        return -15;
    }

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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $useDeclarations = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);

        foreach ($useDeclarations as $declaration) {
            if (!$declaration->isAliased()) {
                continue;
            }
            $shortNameStartPos = strrpos($declaration->getFullName(), '\\');
            $shortName = false === $shortNameStartPos ? $declaration->getFullName() : substr($declaration->getFullName(), $shortNameStartPos + 1);

            if ($declaration->getShortName() !== $shortName) {
                continue;
            }

            $this->removeAlias($tokens, $declaration);
        }
    }

    private function removeAlias(Tokens $tokens, NamespaceUseAnalysis $declaration)
    {
        $asIndex = $tokens->getNextTokenOfKind($declaration->getStartIndex(), [[T_AS]]);
        if (null === $asIndex || $asIndex > $declaration->getEndIndex()) {
            return;
        }

        for ($i = $tokens->getPrevMeaningfulToken($asIndex) + 1; $i <= $declaration->getEndIndex() - 1; ++$i) {
            if ($tokens[$i]->isWhitespace() && !($tokens[$i - 1]->isComment() || $tokens[$i + 1]->isComment())) {
                $tokens->clearAt($i);
            } elseif ($tokens[$i]->isGivenKind([T_AS, T_STRING])) {
                $tokens->clearAt($i);
            }
        }
    }
}
