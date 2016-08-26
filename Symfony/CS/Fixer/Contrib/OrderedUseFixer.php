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
     * Sorting type.
     *
     * @var string
     */
    private static $sortType = self::SORT_ALPHA;

    /**
     * Array of supported sort types.
     *
     * @var string[]
     */
    private static $supportedSortTypes = array(self::SORT_ALPHA, self::SORT_LENGTH);

    /**
     * @param array $sortType
     */
    public static function configure(array $sortType = null)
    {
        // If no configuration was passed, stick to default.
        if (null === $sortType) {
            return;
        }

        // Configuration should contain only one sort type and can not be empty.
        if (count($sortType) !== 1) {
            throw new InvalidFixerConfigurationException('ordered_use', sprintf('Sort type is invalid. Array should contain only one of the parameter: "%s"', implode('", "', self::$supportedSortTypes)));
        }

        $sortType = array_pop($sortType);

        // Check if passed sort type is supported.
        if (!is_string($sortType) || !in_array($sortType, self::$supportedSortTypes, true)) {
            throw new InvalidFixerConfigurationException('ordered_use', sprintf('Sort type is invalid. Array should contain only one of the parameter: "%s"', implode('", "', self::$supportedSortTypes)));
        }

        self::$sortType = $sortType;
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
        if ($first['importType'] !== $second['importType']) {
            return $first['importType'] > $second['importType'] ? 1 : -1;
        }

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

        switch (self::$sortType) {
            case self::SORT_LENGTH:
                uasort($indexes, 'self::sortByLength');
                break;
            default:
                uasort($indexes, 'self::sortAlphabetically');
                break;
        }

        $index = -1;
        $usesOrder = array();

        // Loop trough the index but use original index order
        foreach ($indexes as $v) {
            $usesOrder[$originalIndexes[++$index]] = $v;
        }

        return $usesOrder;
    }
}
