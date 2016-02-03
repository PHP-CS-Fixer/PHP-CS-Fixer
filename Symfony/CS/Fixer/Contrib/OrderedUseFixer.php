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
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class OrderedUseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $namespacesImports = $tokens->getImportUseIndexes(true);
        $usesOrder = array();

        if (!count($namespacesImports)) {
            return $content;
        }

        foreach ($namespacesImports as $uses) {
            $uses = array_reverse($uses);
            $usesOrder = array_replace($usesOrder, $this->getNewOrder($uses, $tokens));
        }

        // First clean the old content
        // This must be done first as the indexes can be scattered
        foreach ($usesOrder as $use) {
            $tokens->clearRange($use[1], $use[2]);
        }

        $usesOrder = array_reverse($usesOrder, true);

        // Now insert the new tokens, starting from the end
        foreach ($usesOrder as $index => $use) {
            $declarationTokens = Tokens::fromCode('<?php use '.$use[0].';');
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
        $a = trim(preg_replace('%/\*(.*)\*/%s', '', $first[0]));
        $b = trim(preg_replace('%/\*(.*)\*/%s', '', $second[0]));

        // Replace backslashes by spaces before sorting for correct sort order
        $a = str_replace('\\', ' ', $a);
        $b = str_replace('\\', ' ', $b);

        return strcasecmp($a, $b);
    }

    private function getNewOrder(array $uses, Tokens $tokens)
    {
        $uses = array_reverse($uses);

        $indexes = array();
        $originalIndexes = array();

        foreach ($uses as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, array(';'));
            $startIndex = $tokens->getTokenNotOfKindSibling($index + 1, 1, array(array(T_WHITESPACE)));

            $namespace = '';
            $index = $startIndex;

            while ($index <= $endIndex) {
                $token = $tokens[$index];

                if ($index === $endIndex || $token->equals(',')) {
                    $indexes[$startIndex] = array($namespace, $startIndex, $index - 1);
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

        uasort($indexes, 'self::sortingCallBack');

        $i = -1;

        $usesOrder = array();

        // Loop trough the index but use original index order
        foreach ($indexes as $v) {
            $usesOrder[$originalIndexes[++$i]] = $v;
        }

        return $usesOrder;
    }
}
