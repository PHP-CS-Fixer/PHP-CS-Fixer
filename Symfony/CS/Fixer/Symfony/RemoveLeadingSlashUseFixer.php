<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
class RemoveLeadingSlashUseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $foundNamespace = $tokens->findGivenKind(T_NAMESPACE);
        if (empty($foundNamespace)) {
            return $content;
        }

        $firstNamespaceIdx = key($foundNamespace);

        $usesIdxs = $tokens->getImportUseIndexes();

        foreach ($usesIdxs as $idx) {
            if ($idx < $firstNamespaceIdx) {
                continue;
            }

            $nextTokenIdx = $tokens->getNextNonWhitespace($idx);
            $nextToken = $tokens[$nextTokenIdx];

            if ($nextToken->isGivenKind(T_NS_SEPARATOR)) {
                $nextToken->clear();
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the MultipleUseFixer (for fix separated use statements as well) and UnusedUseFixer (just for save performance)
        return -20;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Remove leading slashes in use clauses.';
    }
}
