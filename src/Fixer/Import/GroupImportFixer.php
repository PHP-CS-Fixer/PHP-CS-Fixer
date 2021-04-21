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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Volodymyr Kupriienko <vldmr.kuprienko@gmail.com>
 */
final class GroupImportFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There MUST be group use for the same namespaces.',
            [
                new VersionSpecificCodeSample(
                    "<?php\nuse Foo\\Bar;\nuse Foo\\Baz;\n",
                    new VersionSpecification(70000)
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return \PHP_VERSION_ID >= 70000 && $tokens->isTokenKindFound(T_USE);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $useWithSameNamespaces = $this->getSameNamespaces($tokens);

        if ([] === $useWithSameNamespaces) {
            return;
        }

        $this->removeSingleUseStatements($useWithSameNamespaces, $tokens);
        $this->addGroupUseStatements($useWithSameNamespaces, $tokens);
    }

    /**
     * Gets namespace use analyzers with same namespaces.
     *
     * @return NamespaceUseAnalysis[]
     */
    private function getSameNamespaces(Tokens $tokens)
    {
        $useDeclarations = (new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);

        if (0 === \count($useDeclarations)) {
            return [];
        }

        $allNamespaceAndType = array_map(
            function (NamespaceUseAnalysis $useDeclaration) {
                return $this->getNamespaceNameWithSlash($useDeclaration).$useDeclaration->getType();
            },
            $useDeclarations
        );

        $sameNamespaces = array_filter(array_count_values($allNamespaceAndType), function ($count) {
            return $count > 1;
        });
        $sameNamespaces = array_keys($sameNamespaces);

        $sameNamespaceAnalysis = array_filter($useDeclarations, function ($useDeclaration) use ($sameNamespaces) {
            $namespaceNameAndType = $this->getNamespaceNameWithSlash($useDeclaration).$useDeclaration->getType();

            return \in_array($namespaceNameAndType, $sameNamespaces, true);
        });

        usort($sameNamespaceAnalysis, function (NamespaceUseAnalysis $a, NamespaceUseAnalysis $b) {
            $namespaceA = $this->getNamespaceNameWithSlash($a);
            $namespaceB = $this->getNamespaceNameWithSlash($b);

            return \strlen($namespaceA) - \strlen($namespaceB) ?: strcmp($a->getFullName(), $b->getFullName());
        });

        return $sameNamespaceAnalysis;
    }

    /**
     * @param NamespaceUseAnalysis[] $statements
     */
    private function removeSingleUseStatements(array $statements, Tokens $tokens)
    {
        foreach ($statements as $useDeclaration) {
            $index = $useDeclaration->getStartIndex();
            $endIndex = $useDeclaration->getEndIndex();

            $useStatementTokens = [T_USE, T_WHITESPACE, T_STRING, T_NS_SEPARATOR, T_AS, CT::T_CONST_IMPORT, CT::T_FUNCTION_IMPORT];

            while ($index !== $endIndex) {
                if ($tokens[$index]->isGivenKind($useStatementTokens)) {
                    $tokens->clearAt($index);
                }

                ++$index;
            }

            if (isset($tokens[$index]) && $tokens[$index]->equals(';')) {
                $tokens->clearAt($index);
            }

            ++$index;

            if (isset($tokens[$index]) && $tokens[$index]->isGivenKind(T_WHITESPACE)) {
                $tokens->clearAt($index);
            }
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $statements
     */
    private function addGroupUseStatements(array $statements, Tokens $tokens)
    {
        $currentUseDeclaration = null;
        $insertIndex = \array_slice($statements, -1)[0]->getEndIndex();

        while ($tokens[$insertIndex]->isGivenKind([T_COMMENT, T_DOC_COMMENT])) {
            ++$insertIndex;
        }

        foreach ($statements as $index => $useDeclaration) {
            if ($this->areDeclarationsDifferent($currentUseDeclaration, $useDeclaration)) {
                if ($index > 1) {
                    ++$insertIndex;
                }

                $currentUseDeclaration = $useDeclaration;
                $insertIndex += $this->createNewGroup(
                    $tokens,
                    $insertIndex,
                    $useDeclaration,
                    $this->getNamespaceNameWithSlash($currentUseDeclaration)
                );
            } else {
                $newTokens = [
                    new Token(','),
                    new Token([T_WHITESPACE, ' ']),
                ];

                if ($useDeclaration->isAliased()) {
                    $tokens->insertAt($insertIndex + 1, $newTokens);
                    $insertIndex += \count($newTokens);
                    $newTokens = [];

                    $insertIndex += $this->insertToGroupUseWithAlias($tokens, $insertIndex + 1, $useDeclaration);
                }

                $newTokens[] = new Token([T_STRING, $useDeclaration->getShortName()]);

                if (!isset($statements[$index + 1]) || $this->areDeclarationsDifferent($currentUseDeclaration, $statements[$index + 1])) {
                    $newTokens[] = new Token([CT::T_GROUP_IMPORT_BRACE_CLOSE, '}']);
                    $newTokens[] = new Token(';');
                    $newTokens[] = new Token([T_WHITESPACE, "\n"]);
                }

                $tokens->insertAt($insertIndex + 1, $newTokens);
                $insertIndex += \count($newTokens);
            }
        }
    }

    /**
     * @return string
     */
    private function getNamespaceNameWithSlash(NamespaceUseAnalysis $useDeclaration)
    {
        $position = strrpos($useDeclaration->getFullName(), '\\');
        if (false === $position || 0 === $position) {
            return $useDeclaration->getFullName();
        }

        return substr($useDeclaration->getFullName(), 0, $position + 1);
    }

    /**
     * Insert use with alias to the group.
     *
     * @param int $insertIndex
     *
     * @return int
     */
    private function insertToGroupUseWithAlias(Tokens $tokens, $insertIndex, NamespaceUseAnalysis $useDeclaration)
    {
        $newTokens = [
            new Token([T_STRING, substr($useDeclaration->getFullName(), strripos($useDeclaration->getFullName(), '\\') + 1)]),
            new Token([T_WHITESPACE, ' ']),
            new Token([T_AS, 'as']),
            new Token([T_WHITESPACE, ' ']),
        ];

        $tokens->insertAt($insertIndex, $newTokens);

        return \count($newTokens);
    }

    /**
     * Creates new use statement group.
     *
     * @param int    $insertIndex
     * @param string $currentNamespace
     *
     * @return int
     */
    private function createNewGroup(Tokens $tokens, $insertIndex, NamespaceUseAnalysis $useDeclaration, $currentNamespace)
    {
        $insertedTokens = 0;

        if (\count($tokens) === $insertIndex) {
            $tokens->setSize($insertIndex + 1);
        }

        $newTokens = [
            new Token([T_USE, 'use']),
            new Token([T_WHITESPACE, ' ']),
        ];

        if ($useDeclaration->isFunction() || $useDeclaration->isConstant()) {
            $importStatementParams = $useDeclaration->isFunction()
                ? [CT::T_FUNCTION_IMPORT, 'function']
                : [CT::T_CONST_IMPORT, 'const'];

            $newTokens[] = new Token($importStatementParams);
            $newTokens[] = new Token([T_WHITESPACE, ' ']);
        }

        $namespaceParts = array_filter(explode('\\', $currentNamespace));

        foreach ($namespaceParts as $part) {
            $newTokens[] = new Token([T_STRING, $part]);
            $newTokens[] = new Token([T_NS_SEPARATOR, '\\']);
        }

        $newTokens[] = new Token([CT::T_GROUP_IMPORT_BRACE_OPEN, '{']);

        $newTokensCount = \count($newTokens);
        $tokens->insertAt($insertIndex, $newTokens);
        $insertedTokens += $newTokensCount;

        $insertIndex += $newTokensCount;

        if ($useDeclaration->isAliased()) {
            $inserted = $this->insertToGroupUseWithAlias($tokens, $insertIndex + 1, $useDeclaration);
            $insertedTokens += $inserted;
            $insertIndex += $inserted;
        }

        $tokens->insertAt($insertIndex + 1, new Token([T_STRING, $useDeclaration->getShortName()]));
        ++$insertedTokens;

        return $insertedTokens;
    }

    /**
     * Check if namespace use analyses are different.
     *
     * @param null|NamespaceUseAnalysis $analysis1
     * @param null|NamespaceUseAnalysis $analysis2
     *
     * @return bool
     */
    private function areDeclarationsDifferent($analysis1, $analysis2)
    {
        if (null === $analysis1 || null === $analysis2) {
            return true;
        }

        $namespaceName1 = $this->getNamespaceNameWithSlash($analysis1);
        $namespaceName2 = $this->getNamespaceNameWithSlash($analysis2);

        return $namespaceName1 !== $namespaceName2 || $analysis1->getType() !== $analysis2->getType();
    }
}
