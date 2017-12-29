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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 * @author Darius Matulionis <darius@matulionis.lt>
 * @author Adriano Pilger <adriano.pilger@gmail.com>
 */
final class OrderedImportsFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    const IMPORT_TYPE_CLASS = 'class';

    const IMPORT_TYPE_CONST = 'const';

    const IMPORT_TYPE_FUNCTION = 'function';

    const SORT_ALPHA = 'alpha';

    const SORT_LENGTH = 'length';

    /**
     * Array of supported sort types in configuration.
     *
     * @var string[]
     */
    private $supportedSortTypes = [self::IMPORT_TYPE_CLASS, self::IMPORT_TYPE_CONST, self::IMPORT_TYPE_FUNCTION];

    /**
     * Array of supported sort algorithms in configuration.
     *
     * @var string[]
     */
    private $supportedSortAlgorithms = [self::SORT_ALPHA, self::SORT_LENGTH];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Ordering use statements.',
            [
                new CodeSample("<?php\nuse Z; use A;\n"),
                new CodeSample(
'<?php
use Bar1;
use Acme;
use Barr;
use Acme\Bar;
',
                    ['sortAlgorithm' => self::SORT_LENGTH]
                ),
                new VersionSpecificCodeSample(
                    "<?php\nuse function AAA;\nuse const AAB;\nuse AAC;\n",
                    new VersionSpecification(70000)
                ),
                new VersionSpecificCodeSample(
'<?php
use const AAAA;
use const BBB;

use Bar;
use AAC;
use Acme;

use function CCC\AA;
use function DDD;
',
                    new VersionSpecification(70000),
                    [
                        'sortAlgorithm' => self::SORT_LENGTH,
                        'importsOrder' => [
                            self::IMPORT_TYPE_CONST,
                            self::IMPORT_TYPE_CLASS,
                            self::IMPORT_TYPE_FUNCTION,
                        ],
                    ]
                ),
                new VersionSpecificCodeSample(
                    '<?php
use const BBB;
use const AAAA;

use Acme;
use AAC;
use Bar;

use function DDD;
use function CCC\AA;
',
                    new VersionSpecification(70000),
                    [
                        'sortAlgorithm' => self::SORT_ALPHA,
                        'importsOrder' => [
                            self::IMPORT_TYPE_CONST,
                            self::IMPORT_TYPE_CLASS,
                            self::IMPORT_TYPE_FUNCTION,
                        ],
                    ]
                ),
            ]
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
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $namespacesImports = $tokensAnalyzer->getImportUseIndexes(true);

        if (0 === count($namespacesImports)) {
            return;
        }

        $usesOrder = [];
        foreach ($namespacesImports as $uses) {
            $usesOrder[] = $this->getNewOrder(array_reverse($uses), $tokens);
        }
        $usesOrder = array_replace(...$usesOrder);

        $usesOrder = array_reverse($usesOrder, true);
        $mapStartToEnd = [];

        foreach ($usesOrder as $use) {
            $mapStartToEnd[$use['startIndex']] = $use['endIndex'];
        }

        // Now insert the new tokens, starting from the end
        foreach ($usesOrder as $index => $use) {
            $declarationTokens = Tokens::fromCode('<?php use '.$use['namespace'].';');
            $declarationTokens->clearRange(0, 2); // clear `<?php use `
            $declarationTokens->clearAt(count($declarationTokens) - 1); // clear `;`
            $declarationTokens->clearEmptyTokens();

            $tokens->overrideRange($index, $mapStartToEnd[$index], $declarationTokens);
            if ($use['group']) {
                // a group import must start with `use` and cannot be part of comma separated import list
                $prev = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prev]->equals(',')) {
                    $tokens[$prev] = new Token(';');
                    $tokens->insertAt($prev + 1, new Token([T_USE, 'use']));

                    if (!$tokens[$prev + 2]->isWhitespace()) {
                        $tokens->insertAt($prev + 2, new Token([T_WHITESPACE, ' ']));
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $supportedSortTypes = $this->supportedSortTypes;

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('sortAlgorithm', 'whether the statements should be sorted alphabetically or by length'))
                ->setAllowedValues($this->supportedSortAlgorithms)
                ->setDefault(self::SORT_ALPHA)
                ->getOption(),
            (new FixerOptionBuilder('importsOrder', 'Defines the order of import types.'))
                ->setAllowedTypes(['array', 'null'])
                ->setAllowedValues([static function ($value) use ($supportedSortTypes) {
                    if (null !== $value) {
                        $missing = array_diff($supportedSortTypes, $value);
                        if (count($missing)) {
                            throw new InvalidOptionsException(sprintf(
                                'Missing sort %s "%s".',
                                1 === count($missing) ? 'type' : 'types',
                                implode('", "', $missing)
                            ));
                        }

                        $unknown = array_diff($value, $supportedSortTypes);
                        if (count($unknown)) {
                            throw new InvalidOptionsException(sprintf(
                                'Unknown sort %s "%s".',
                                1 === count($unknown) ? 'type' : 'types',
                                implode('", "', $unknown)
                            ));
                        }
                    }

                    return true;
                }])
                ->setDefault(null)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Ordering use statements.';
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
    private function sortAlphabetically(array $first, array $second)
    {
        if ($first['importType'] !== $second['importType']) {
            return $first['importType'] > $second['importType'] ? 1 : -1;
        }

        // Replace backslashes by spaces before sorting for correct sort order
        $firstNamespace = str_replace('\\', ' ', $this->prepareNamespace($first['namespace']));
        $secondNamespace = str_replace('\\', ' ', $this->prepareNamespace($second['namespace']));

        return strcasecmp($firstNamespace, $secondNamespace);
    }

    /**
     * This method is used for sorting the uses statements in a namespace by length.
     *
     * @param string[] $first
     * @param string[] $second
     *
     * @return int
     *
     * @internal
     */
    private function sortByLength(array $first, array $second)
    {
        $firstNamespace = $this->prepareNamespace($first['namespace']);
        $secondNamespace = $this->prepareNamespace($second['namespace']);

        $firstNamespaceLength = strlen($firstNamespace);
        $secondNamespaceLength = strlen($secondNamespace);

        if ($firstNamespaceLength === $secondNamespaceLength) {
            $sortResult = strcasecmp($firstNamespace, $secondNamespace);
        } else {
            $sortResult = $firstNamespaceLength > $secondNamespaceLength ? 1 : -1;
        }

        return $sortResult;
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    private function prepareNamespace($namespace)
    {
        return trim(preg_replace('%/\*(.*)\*/%s', '', $namespace));
    }

    private function getNewOrder(array $uses, Tokens $tokens)
    {
        $indexes = [];
        $originalIndexes = [];
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        for ($i = count($uses) - 1; $i >= 0; --$i) {
            $index = $uses[$i];

            $startIndex = $tokens->getTokenNotOfKindSibling($index + 1, 1, [[T_WHITESPACE]]);
            $endIndex = $tokens->getNextTokenOfKind($startIndex, [';', [T_CLOSE_TAG]]);
            $previous = $tokens->getPrevMeaningfulToken($endIndex);

            $group = $tokens[$previous]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_CLOSE);
            if ($tokens[$startIndex]->isGivenKind([CT::T_CONST_IMPORT])) {
                $type = self::IMPORT_TYPE_CONST;
            } elseif ($tokens[$startIndex]->isGivenKind([CT::T_FUNCTION_IMPORT])) {
                $type = self::IMPORT_TYPE_FUNCTION;
            } else {
                $type = self::IMPORT_TYPE_CLASS;
            }

            $namespaceTokens = [];
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
                        $parts = [];
                        $firstIndent = '';
                        $separator = ', ';
                        $lastIndent = '';

                        for ($k1 = $k + 1; $k1 < $namespaceTokensCount; ++$k1) {
                            $comment = '';
                            $namespacePart = '';
                            for ($k2 = $k1;; ++$k2) {
                                if ($namespaceTokens[$k2]->equalsAny([',', [CT::T_GROUP_IMPORT_BRACE_CLOSE]])) {
                                    break;
                                }

                                if ($namespaceTokens[$k2]->isComment()) {
                                    $comment .= $namespaceTokens[$k2]->getContent();

                                    continue;
                                }

                                // if there is any line ending inside the group import, it should be indented properly
                                if (
                                    '' === $firstIndent &&
                                    $namespaceTokens[$k2]->isWhitespace() &&
                                    false !== strpos($namespaceTokens[$k2]->getContent(), $lineEnding)
                                ) {
                                    $lastIndent = $lineEnding;
                                    $firstIndent = $lineEnding.$this->whitespacesConfig->getIndent();
                                    $separator = ','.$firstIndent;
                                }

                                $namespacePart .= $namespaceTokens[$k2]->getContent();
                            }

                            $namespacePart = trim($namespacePart);
                            $comment = trim($comment);
                            if ('' !== $comment) {
                                $namespacePart .= ' '.$comment;
                            }

                            $parts[] = $namespacePart;

                            $k1 = $k2;
                        }

                        $sortedParts = $parts;
                        sort($parts);

                        // check if the order needs to be updated, otherwise don't touch as we might change valid CS (to other valid CS).
                        if ($sortedParts === $parts) {
                            $namespace = Tokens::fromArray($namespaceTokens)->generateCode();
                        } else {
                            $namespace .= $firstIndent.implode($separator, $parts).$lastIndent.'}';
                        }
                    } else {
                        $namespace = Tokens::fromArray($namespaceTokens)->generateCode();
                    }

                    $indexes[$startIndex] = [
                        'namespace' => $namespace,
                        'startIndex' => $startIndex,
                        'endIndex' => $index - 1,
                        'importType' => $type,
                        'group' => $group,
                    ];

                    $originalIndexes[] = $startIndex;

                    if ($index === $endIndex) {
                        break;
                    }

                    $namespaceTokens = [];
                    $nextPartIndex = $tokens->getTokenNotOfKindSibling($index, 1, [[','], [T_WHITESPACE]]);
                    $startIndex = $nextPartIndex;
                    $index = $nextPartIndex;

                    continue;
                }

                $namespaceTokens[] = $token;
                ++$index;
            }
        }

        // Is sort types provided, sorting by groups and each group by algorithm
        if ($this->configuration['importsOrder']) {
            // Grouping indexes by import type.
            $groupedByTypes = [];
            foreach ($indexes as $startIndex => $item) {
                $groupedByTypes[$item['importType']][$startIndex] = $item;
            }

            // Sorting each group by algorithm.
            foreach ($groupedByTypes as $type => $indexes) {
                $groupedByTypes[$type] = $this->sortByAlgorithm($indexes);
            }

            // Ordering groups
            $sortedGroups = [];
            foreach ($this->configuration['importsOrder'] as $type) {
                if (isset($groupedByTypes[$type]) && !empty($groupedByTypes[$type])) {
                    foreach ($groupedByTypes[$type] as $startIndex => $item) {
                        $sortedGroups[$startIndex] = $item;
                    }
                }
            }
            $indexes = $sortedGroups;
        } else {
            // Sorting only by algorithm
            $indexes = $this->sortByAlgorithm($indexes);
        }

        $index = -1;
        $usesOrder = [];

        // Loop trough the index but use original index order
        foreach ($indexes as $v) {
            $usesOrder[$originalIndexes[++$index]] = $v;
        }

        return $usesOrder;
    }

    /**
     * @param $indexes
     *
     * @return array
     */
    private function sortByAlgorithm($indexes)
    {
        if (self::SORT_ALPHA === $this->configuration['sortAlgorithm']) {
            uasort($indexes, [$this, 'sortAlphabetically']);
        } elseif (self::SORT_LENGTH === $this->configuration['sortAlgorithm']) {
            uasort($indexes, [$this, 'sortByLength']);
        } else {
            throw new \LogicException(sprintf('Sort algorithm "%s" is not supported.', $this->configuration['sortAlgorithm']));
        }

        return $indexes;
    }
}
