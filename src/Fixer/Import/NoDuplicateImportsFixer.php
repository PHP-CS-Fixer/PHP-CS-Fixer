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

namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

final class NoDuplicateImportsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The should be duplicate `use` imports.',
            [new CodeSample("<?php\nuse Throwable;\nuse Throwable; // duplicate\n")]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoExtraBlankLinesFixer, NoTrailingWhitespaceFixer, NoWhitespaceInBlankLineFixer.
     * Must run after NoUnneededImportAliasFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_USE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $namespaceUsesAnalyzer = new NamespaceUsesAnalyzer();
        $namespacesAnalyzer = new NamespacesAnalyzer();

        foreach ($namespacesAnalyzer->getDeclarations($tokens) as $namespaceAnalysis) {
            $namespaceUseAnalyses = $namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespaceAnalysis);

            foreach ($this->getDuplicatesInUsesInNamespace($namespaceUseAnalyses) as $duplicateNamespaceUseAnalysis) {
                $this->removeDuplicatesUse($tokens, $duplicateNamespaceUseAnalysis);
            }
        }
    }

    private function removeDuplicatesUse(Tokens $tokens, NamespaceUseAnalysis $duplicate): void
    {
        $index = $duplicate->getEndIndex();

        if ($tokens[$index]->equals(';')) { // do not remove `? >`
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }

        while (true) {
            $index = $tokens->getPrevMeaningfulToken($index);

            if (null === $index || $index < $duplicate->getStartIndex()) {
                break;
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $namespaceUseAnalyses
     */
    private function getDuplicatesInUsesInNamespace(array $namespaceUseAnalyses): iterable
    {
        $uses = [];

        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if ($namespaceUseAnalysis->isAliased()) {
                continue;
            }

            $name = sprintf('{%d}#%s', $namespaceUseAnalysis->getType(), ltrim($namespaceUseAnalysis->getFullName(), '\\'));

            if (isset($uses[$name])) {
                yield $namespaceUseAnalysis;
            } else {
                $uses[$name] = true;
            }
        }
    }
}
