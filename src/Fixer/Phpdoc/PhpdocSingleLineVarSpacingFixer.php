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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for part of rule defined in PSR5 ¶7.22.
 *
 * @author SpacePossum
 */
final class PhpdocSingleLineVarSpacingFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_COMMENT, T_DOC_COMMENT));
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        /** @var Token $token */
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_DOC_COMMENT)) {
                $token->setContent($this->fixTokenContent($token->getContent()));
                continue;
            }

            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            $fixedContent = $this->fixTokenContent($content);
            if ($content !== $fixedContent) {
                $tokens->overrideAt($index, array(T_DOC_COMMENT, $fixedContent));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be ran after PhpdocNoAliasTagFixer.
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDescription()
    {
        return 'Single line @var PHPDoc should have proper spacing.';
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function fixTokenContent($content)
    {
        return preg_replace_callback(
            '#^/\*\*[ \t]*@var[ \t]+(\S+)[ \t]*(\$\S+)?[ \t]*([^\n]*)\*/$#',
            function (array $matches) {
                $content = '/** @var';
                for ($i = 1, $m = count($matches); $i < $m; ++$i) {
                    if ('' !== $matches[$i]) {
                        $content .= ' '.$matches[$i];
                    }
                }

                $content = rtrim($content);

                return $content.' */';
            },
            $content
        );
    }
}
