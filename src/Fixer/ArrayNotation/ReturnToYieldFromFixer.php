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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class ReturnToYieldFromFixer extends AbstractFixer
{
    private const SUPPORTED_TYPES = [
        'iterable' => 'iterable',
        'iterator' => 'Iterator',
        'traversable' => 'Traversable',
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            sprintf(
                'When function return type is iterable (%s) and it starts with `return` then it must be changed to `yield from`.',
                Utils::naturalLanguageJoinWithBackticks(self::SUPPORTED_TYPES)
            ),
            [new CodeSample('<?php function giveMeData(): iterable {
    return [1, 2, 3];
}
')],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_RETURN);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before YieldFromArrayToYieldsFixer.
     * Must run after PhpUnitDataProviderReturnTypeFixer, PhpdocToReturnTypeFixer.
     */
    public function getPriority(): int
    {
        return 1;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens->findGivenKind(T_RETURN) as $index => $token) {
            if (!$this->shouldBeFixed($tokens, $index)) {
                continue;
            }

            $tokens[$index] = new Token([T_YIELD_FROM, 'yield from']);
        }
    }

    private function shouldBeFixed(Tokens $tokens, int $returnIndex): bool
    {
        $beforeReturnIndex = $tokens->getPrevMeaningfulToken($returnIndex);
        if (!$tokens[$beforeReturnIndex]->equals('{')) {
            return false;
        }

        $returnTypeIndex = $tokens->getPrevMeaningfulToken($beforeReturnIndex);
        if (!$tokens[$returnTypeIndex]->isGivenKind(T_STRING)) {
            return false;
        }

        $returnType = strtolower($tokens[$returnTypeIndex]->getContent());
        if (!isset(self::SUPPORTED_TYPES[$returnType])) {
            return false;
        }

        $beforeReturnTypeIndex = $tokens->getPrevMeaningfulToken($returnTypeIndex);
        if ($tokens[$beforeReturnTypeIndex]->isGivenKind(CT::T_TYPE_COLON)) {
            return !$this->isTypeImportedFromVendor($tokens, $returnTypeIndex);
        }

        if (!$tokens[$beforeReturnTypeIndex]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        $beforeBeforeReturnTypeIndex = $tokens->getPrevMeaningfulToken($beforeReturnTypeIndex);

        return $tokens[$beforeBeforeReturnTypeIndex]->isGivenKind(CT::T_TYPE_COLON);
    }

    private function isTypeImportedFromVendor(Tokens $tokens, int $returnTypeIndex): bool
    {
        $returnType = strtolower($tokens[$returnTypeIndex]->getContent());
        if ('iterable' === $returnType) {
            return false;
        }

        foreach ($tokens->getNamespaceDeclarations() as $namespace) {
            if ($returnTypeIndex < $namespace->getScopeStartIndex()) {
                continue;
            }
            if ($returnTypeIndex > $namespace->getScopeEndIndex()) {
                continue;
            }

            foreach ((new NamespaceUsesAnalyzer())->getDeclarationsInNamespace($tokens, $namespace) as $use) {
                $importFullName = ltrim($use->getFullName(), '\\');
                if (str_ends_with(strtolower($importFullName), '\\'.$returnType)) {
                    return true;
                }
            }
        }

        return false;
    }
}
