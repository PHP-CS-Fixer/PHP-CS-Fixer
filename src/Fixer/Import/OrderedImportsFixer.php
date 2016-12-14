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
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class OrderedImportsFixer extends AbstractFixer
{
    const IMPORT_TYPE_CLASS = 1;
    const IMPORT_TYPE_CONST = 2;
    const IMPORT_TYPE_FUNCTION = 3;

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $namespacesImports = $tokensAnalyzer->getImportUseIndexes(true);

        if (0 === count($namespacesImports)) {
            return;
        }

        $usesOrder = array();
        foreach ($namespacesImports as $uses) {
            $usesOrder = array_replace($usesOrder, $this->getNewOrder(array_reverse($uses), $tokens));
        }

        $usesOrder = array_reverse($usesOrder, true);
        $mapStartToEnd = array();

        foreach ($usesOrder as $use) {
            $mapStartToEnd[$use['startIndex']] = $use['endIndex'];
        }

        // Now insert the new tokens, starting from the end
        foreach ($usesOrder as $index => $use) {
            $declarationTokens = Tokens::fromCode('<?php use '.$use['namespace'].';');
            $declarationTokens->clearRange(0, 2); // clear `<?php use `
            $declarationTokens[count($declarationTokens) - 1]->clear(); // clear `;`
            $declarationTokens->clearEmptyTokens();

            $tokens->overrideRange($index, $mapStartToEnd[$index], $declarationTokens);
            if ($use['group']) {
                // a group import must start with `use` and cannot be part of comma separated import list
                $prev = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prev]->equals(',')) {
                    $tokens[$prev]->setContent(';');
                    $tokens->insertAt($prev + 1, new Token(array(T_USE, 'use')));
                    if (!$tokens[$prev + 2]->isWhitespace()) {
                        $tokens->insertAt($prev + 2, new Token(array(T_WHITESPACE, ' ')));
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
            'Ordering use statements.',
            array(
                new CodeSample("<?php\nuse Z; use A;"),
                new VersionSpecificCodeSample(
                    "<?php\nuse function AAA;\nuse const AAB;\nuse AAC;",
                    new VersionSpecification(70000)
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the NoLeadingImportSlashFixer
        return -30;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_USE);
    }

    /**
     * This method is used for sorting the uses in a namespace.
     *
     * @param string[] $first
     * @param string[] $second
     *
     * @return int
     *
     * @internal
     */
    public static function sortingCallBack(array $first, array $second)
    {
        if ($first['importType'] !== $second['importType']) {
            return $first['importType'] > $second['importType'] ? 1 : -1;
        }

        $firstNamespace = trim(preg_replace('%/\*(.*)\*/%s', '', $first['namespace']));
        $secondNamespace = trim(preg_replace('%/\*(.*)\*/%s', '', $second['namespace']));

        // Replace backslashes by spaces before sorting for correct sort order
        return strcasecmp(
            str_replace('\\', ' ', $firstNamespace),
            str_replace('\\', ' ', $secondNamespace)
        );
    }

    private function getNewOrder(array $uses, Tokens $tokens)
    {
        $indexes = array();
        $originalIndexes = array();

        for ($i = count($uses) - 1; $i >= 0; --$i) {
            $index = $uses[$i];

            $startIndex = $tokens->getTokenNotOfKindSibling($index + 1, 1, array(array(T_WHITESPACE)));
            $endIndex = $tokens->getNextTokenOfKind($startIndex, array(';', array(T_CLOSE_TAG)));
            $previous = $tokens->getPrevMeaningfulToken($endIndex);

            $group = $tokens[$previous]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_CLOSE);
            if ($tokens[$startIndex]->isGivenKind(array(CT::T_CONST_IMPORT))) {
                $type = self::IMPORT_TYPE_CONST;
            } elseif ($tokens[$startIndex]->isGivenKind(array(CT::T_FUNCTION_IMPORT))) {
                $type = self::IMPORT_TYPE_FUNCTION;
            } else {
                $type = self::IMPORT_TYPE_CLASS;
            }

            $namespaceTokens = array();
            $index = $startIndex;

            while ($index <= $endIndex) {
                $token = $tokens[$index];

                if ($index === $endIndex || (!$group && $token->equals(','))) {
                    if ($group) {
                        // if group import, sort the items within the group definition

                        // figure out where the list of namespace parts within the group def. starts
                        $namespaceTokensCount = count($namespaceTokens) - 1;
                        $namespace = '';
                        for ($k = 0; $k < $namespaceTokensCount; ++$k) {
                            if ($namespaceTokens[$k]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                                $namespace .= '{';
                                break;
                            }

                            $namespace .= $namespaceTokens[$k]->getContent();
                        }

                        // fetch all parts, split up in an array of strings, move comments to the end
                        $parts = array();
                        for ($k1 = $k + 1; $k1 < $namespaceTokensCount; ++$k1) {
                            $comment = '';
                            $namespacePart = '';
                            for ($k2 = $k1; ; ++$k2) {
                                if ($namespaceTokens[$k2]->equalsAny(array(',', array(CT::T_GROUP_IMPORT_BRACE_CLOSE)))) {
                                    break;
                                }

                                if ($namespaceTokens[$k2]->isComment()) {
                                    $comment .= $namespaceTokens[$k2]->getContent();

                                    continue;
                                }

                                $namespacePart .= $namespaceTokens[$k2]->getContent();
                            }

                            $namespacePart = trim($namespacePart);
                            $comment = trim($comment);
                            if ('' !== $comment) {
                                $namespacePart .= ' '.$comment;
                            }

                            $parts[] = $namespacePart.', ';

                            $k1 = $k2;
                        }

                        $sortedParts = $parts;
                        sort($parts);

                        // check if the order needs to be updated, otherwise don't touch as we might change valid CS (to other valid CS).
                        if ($sortedParts === $parts) {
                            $namespace = Tokens::fromArray($namespaceTokens)->generateCode();
                        } else {
                            $namespace .= substr(implode('', $parts), 0, -2).'}';
                        }
                    } else {
                        $namespace = Tokens::fromArray($namespaceTokens)->generateCode();
                    }

                    $indexes[$startIndex] = array(
                        'namespace' => $namespace,
                        'startIndex' => $startIndex,
                        'endIndex' => $index - 1,
                        'importType' => $type,
                        'group' => $group,
                    );

                    $originalIndexes[] = $startIndex;

                    if ($index === $endIndex) {
                        break;
                    }

                    $namespaceTokens = array();
                    $nextPartIndex = $tokens->getTokenNotOfKindSibling($index, 1, array(array(','), array(T_WHITESPACE)));
                    $startIndex = $nextPartIndex;
                    $index = $nextPartIndex;

                    continue;
                }

                $namespaceTokens[] = $token;
                ++$index;
            }
        }

        uasort($indexes, 'self::sortingCallBack');

        $index = -1;
        $usesOrder = array();

        // Loop trough the index but use original index order
        foreach ($indexes as $v) {
            $usesOrder[$originalIndexes[++$index]] = $v;
        }

        return $usesOrder;
    }
}
