<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Graham Campbell <graham@mineuk.com>
 */
abstract class AbstractOrderedUseFixer extends AbstractFixer
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
            $usesOrder = array_replace($usesOrder, self::getNewOrder($uses, $tokens));
        }

        // First clean the old content
        // This must be done first as the indexes can be scattered
        foreach ($usesOrder as $use) {
            for ($i = $use[1]; $i <= $use[2]; ++$i) {
                $tokens[$i]->clear();
            }
        }

        $usesOrder = array_reverse($usesOrder, true);

        // Now insert the new tokens, starting from the end
        foreach ($usesOrder as $index => $use) {
            $declarationTokens = Tokens::fromCode('<?php use '.$use[0].';');
            $declarationTokens[0]->clear(); // clear `<?php`
            $declarationTokens[1]->clear(); // clear `use`
            $declarationTokens[2]->clear(); // clear `space`
            $declarationTokens[count($declarationTokens) - 1]->clear(); // clear `;`

            $tokens->insertAt($index, $declarationTokens);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the MultipleUseFixer
        return -10;
    }

    private static function getNewOrder(array $uses, Tokens $tokens)
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
                if ($token->equals(',') || $index === $endIndex) {
                    $indexes[$startIndex] = array($namespace, $startIndex, $index - 1);
                    $originalIndexes[] = $startIndex;

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

        uasort($indexes, 'static::sortingCallBack');

        $i = -1;

        $usesOrder = array();

        // Loop trough the index but use original index order
        foreach ($indexes as $v) {
            $usesOrder[$originalIndexes[++$i]] = $v;
        }

        return $usesOrder;
    }

    protected static function sortAlphabetically($first, $second)
    {
        $a = trim(preg_replace('%/\*(.*)\*/%s', '', $first));
        $b = trim(preg_replace('%/\*(.*)\*/%s', '', $second));

        return strcasecmp($a, $b);
    }
}
