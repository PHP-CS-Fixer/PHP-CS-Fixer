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

namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @internal
 */
class NamespaceUsesAnalyzer
{
    /**
     * @param Tokens $tokens
     *
     * @return array
     */
    public function getDeclarationsFromTokens(Tokens $tokens)
    {
        $tokenAnalyzer = new TokensAnalyzer($tokens);
        $useIndexes = $tokenAnalyzer->getImportUseIndexes();

        return $this->getDeclarations($tokens, $useIndexes);
    }

    /**
     * @param Tokens $tokens
     * @param array  $useIndexes
     *
     * @return array
     */
    public function getDeclarations(Tokens $tokens, array $useIndexes)
    {
        $uses = [];

        foreach ($useIndexes as $index) {
            $declarationEndIndex = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]);
            $declarationContent = $tokens->generatePartialCode($index + 1, $declarationEndIndex - 1);
            if (
                false !== strpos($declarationContent, ',')    // ignore multiple use statements that should be split into few separate statements (for example: `use BarB, BarC as C;`)
                || false !== strpos($declarationContent, '{') // do not touch group use declarations until the logic of this is added (for example: `use some\a\{ClassD};`)
            ) {
                continue;
            }

            $declarationParts = preg_split('/\s+as\s+/i', $declarationContent);

            if (1 === count($declarationParts)) {
                $fullName = $declarationContent;
                $declarationParts = explode('\\', $fullName);
                $shortName = end($declarationParts);
                $aliased = false;
            } else {
                list($fullName, $shortName) = $declarationParts;
                $declarationParts = explode('\\', $fullName);
                $aliased = $shortName !== end($declarationParts);
            }

            $shortName = trim($shortName);

            $uses[$shortName] = [
                'fullName' => trim($fullName),
                'shortName' => $shortName,
                'aliased' => $aliased,
                'start' => $index,
                'end' => $declarationEndIndex,
            ];
        }

        return $uses;
    }
}
