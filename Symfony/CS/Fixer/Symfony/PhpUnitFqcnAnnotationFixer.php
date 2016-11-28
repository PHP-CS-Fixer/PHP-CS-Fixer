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

namespace Symfony\CS\Fixer\Symfony;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
final class PhpUnitFqcnAnnotationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $token->setContent(preg_replace(
                    '~^(\s*\*\s*@(?:expectedException|covers|coversDefaultClass|uses)\h+)(\w.*)$~m', '$1\\\\$2',
                    $token->getContent()
                ));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHPUnit annotations should be a FQCNs including a root namespace.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before UnusedUseFixer
        return -9;
    }
}
