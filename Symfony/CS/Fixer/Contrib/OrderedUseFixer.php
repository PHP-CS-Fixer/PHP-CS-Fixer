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

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\ConfigurationException\InvalidFixerConfigurationException;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Darius Matulionis <darius@matulionis.lt>
 * @author SpacePossum
 */
class OrderedUseFixer extends AbstractFixer
{
    const IMPORT_TYPE_CLASS = 1;

    const IMPORT_TYPE_CONST = 2;

    const IMPORT_TYPE_FUNCTION = 3;

    const SORT_ALPHA = 'alpha';

    const SORT_LENGTH = 'length';

    /**
     * Array of import types by which use statements should be ordered. If not defined only sort algorithm will be applied.
     *
     * @var null | int[]
     */
    private static $sortTypesOrder = null;

    /**
     * Array of supported sort types.
     *
     * @var string[]
     */
    private static $supportedSortTypes = array(self::IMPORT_TYPE_CLASS, self::IMPORT_TYPE_CONST, self::IMPORT_TYPE_FUNCTION);

    /**
     * Sorting type.
     *
     * @var string
     */
    private static $sortAlgorithm = self::SORT_ALPHA;

    /**
     * Array of supported sort algorithms.
     *
     * @var string[]
     */
    private static $supportedSortAlgorithms = array(self::SORT_ALPHA, self::SORT_LENGTH);

    /**
     * @param array $configuration
     */
    public static function configure(array $configuration = null)
    {
        // If no configuration was passed, stick to default.
        if (null === $configuration) {
            return;
        }

        // Check if configuration has all required parameters.
        if (!array_key_exists('typesOrder', $configuration) || !array_key_exists('sortAlgorithm', $configuration)) {
            throw new InvalidFixerConfigurationException('ordered_use', sprintf('Configuration array should be composed of: "typesOrder" and "sortAlgorithm".'));
        }

        /*** Sort types order configuration ***/

        $typesOrder = $configuration['typesOrder'];

        // If no import types order was provided, we will sort only by algorithm.
        if (null !== $typesOrder) {
            if (!is_array($typesOrder) || count($typesOrder) != count(self::$supportedSortTypes)) {
                throw new InvalidFixerConfigurationException('ordered_use', sprintf('$configuration["typesOrder"] should be array and should be composed of all import types in desired order.'));
            }

            // Check if all provided sort types are supported.
            foreach ($typesOrder as $type) {
                if (!in_array($type, self::$supportedSortTypes)) {
                    throw new InvalidFixerConfigurationException('ordered_use', sprintf('$configuration["typesOrder"] should be array and should be composed of all import types in desired order.'));
                }
            }

            self::$sortTypesOrder = $typesOrder;
        }

        /*** Sort algorithm configuration ***/

        $sortAlgorithm = $configuration['sortAlgorithm'];
        // If no configuration was passed, stick to default.
        if (null === $sortAlgorithm) {
            return;
        }

        // Check if passed sort type is supported.
        if (!is_string($sortAlgorithm) || !in_array($sortAlgorithm, self::$supportedSortAlgorithms, true)) {
            throw new InvalidFixerConfigurationException('ordered_use', sprintf('Sort algorithm is invalid. Should br one of the: "%s"', implode('", "', self::$supportedSortAlgorithms)));
        }

        self::$sortAlgorithm = $sortAlgorithm;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $namespacesImports = $tokens->getImportUseIndexes(true);

        if (0 === count($namespacesImports)) {
            return $content;
        }

        $usesOrder = array();
        foreach ($namespacesImports as $uses) {
            $usesOrder = array_replace($usesOrder, $this->getNewOrder(array_reverse($uses), $tokens));
        }

        // First clean the old content
        // This must be done first as the indexes can be scattered
        foreach ($usesOrder as $use) {
            $tokens->clearRange($use['startIndex'], $use['endIndex']);
        }

        $usesOrder = array_reverse($usesOrder, true);

        // Now insert the new tokens, starting from the end
        foreach ($usesOrder as $index => $use) {
            $declarationTokens = Tokens::fromCode('<?php use '.$use['namespace'].';');
            $declarationTokens->clearRange(0, 2); // clear `<?php use `
            $declarationTokens[count($declarationTokens) - 1]->clear(); // clear `;`
            $declarationTokens->clearEmptyTokens();

            $tokens->insertAt($index, $declarationTokens);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ordering use statements.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the RemoveLeadingSlashUseFixer
        return -30;
    }

    /**
     * Prepare namespace for sorting.
     *
     * @param string $namespace
     *
     * @return string
     */
    private static function prepareNamespace($namespace)
    {
        return trim(preg_replace('%/\*(.*)\*/%s', '', $namespace));
    }

    /**
     * This method is used for sorting the uses statements in alphabetical order.
     *
     * @param string[] $first
     * @param string[] $second
     *
     * @return int
     *
     * @internal
     */
    public static function sortAlphabetically(array $first, array $second)
    {
        $firstNamespace = self::prepareNamespace($first['namespace']);
        $secondNamespace = self::prepareNamespace($second['namespace']);

        // Replace backslashes by spaces before sorting for correct sort order
        $firstNamespace = str_replace('\\', ' ', $firstNamespace);
        $secondNamespace = str_replace('\\', ' ', $secondNamespace);

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
    public function sortByLength(array $first, array $second)
    {
        $firstNamespace = self::prepareNamespace($first['namespace']);
        $secondNamespace = self::prepareNamespace($second['namespace']);

        $firstNamespaceLength = strlen($firstNamespace);
        $secondNamespaceLength = strlen($secondNamespace);

        if ($firstNamespaceLength === $secondNamespaceLength) {
            $sortResult = strcasecmp($firstNamespace, $secondNamespace);
        } else {
            $sortResult = $firstNamespaceLength > $secondNamespaceLength ? 1 : -1;
        }

        return $sortResult;
    }

    private function getNewOrder(array $uses, Tokens $tokens)
    {
        $uses = array_reverse($uses);

        $indexes = array();
        $originalIndexes = array();

        foreach ($uses as $index) {
            $startIndex = $tokens->getTokenNotOfKindSibling($index + 1, 1, array(array(T_WHITESPACE)));
            $endIndex = $tokens->getNextTokenOfKind($startIndex, array(';', array(T_CLOSE_TAG)));
            $previous = $tokens->getPrevMeaningfulToken($endIndex);

            $group = $tokens[$previous]->equals('}');
            if ($tokens[$startIndex]->isGivenKind(array(T_CONST))) {
                $type = self::IMPORT_TYPE_CONST;
            } elseif ($tokens[$startIndex]->isGivenKind(array(T_FUNCTION))) {
                $type = self::IMPORT_TYPE_FUNCTION;
            } else {
                $type = self::IMPORT_TYPE_CLASS;
            }

            $namespace = '';
            $index = $startIndex;

            while ($index <= $endIndex) {
                $token = $tokens[$index];

                if ($index === $endIndex || (!$group && $token->equals(','))) {
                    $indexes[$startIndex] = array(
                        'namespace' => $namespace,
                        'startIndex' => $startIndex,
                        'endIndex' => $index - 1,
                        'importType' => $type,
                    );

                    $originalIndexes[] = $startIndex;

                    if ($index === $endIndex) {
                        break;
                    }

                    $namespace = '';
                    $nextPartIndex = $tokens->getTokenNotOfKindSibling($index, 1, array(array(','), array(T_WHITESPACE)));
                    $startIndex = $nextPartIndex;
                    $index = $nextPartIndex;

                    continue;
                }

                $namespace .= $token->getContent();
                ++$index;
            }
        }

        // Is sort types provided, sorting by groups and each group by algorithm
        if (self::$sortTypesOrder) {

            // Grouping indexes by import type.
            $groupedByTypes = array();
            foreach ($indexes as $startIndex => $item) {
                $groupedByTypes[$item['importType']][$startIndex] = $item;
            }

            // Sorting each group by algorithm.
            foreach ($groupedByTypes as $type => $indexes) {
                $groupedByTypes[$type] = $this->sortByAlgorithm($indexes);
            }

            // Ordering groups
            $sortedGroups = array();
            foreach (self::$sortTypesOrder as $type) {
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
        $usesOrder = array();

        // Loop trough the index but use original index order
        foreach ($indexes as $v) {
            $usesOrder[$originalIndexes[++$index]] = $v;
        }

        return $usesOrder;
    }

    /**
     * @param $indexes
     * @return array
     */
    private function sortByAlgorithm($indexes)
    {
        switch (self::$sortAlgorithm) {
            case self::SORT_LENGTH:
                uasort($indexes, 'self::sortByLength');
                break;
            default:
                uasort($indexes, 'self::sortAlphabetically');
                break;
        }
        return $indexes;
    }
}
