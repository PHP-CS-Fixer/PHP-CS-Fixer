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

namespace PhpCsFixer\Fixer\PhpTag;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfigAwareInterface;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
final class BlankLineAfterOpeningTagFixer extends AbstractFixer implements WhitespacesFixerConfigAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_OPEN_TAG);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();

        // ignore files with short open tag and ignore non-monolithic files
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG) || !$tokens->isMonolithicPhp()) {
            return;
        }

        $newlineFound = false;
        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->isWhitespace() && false !== strpos($token->getContent(), "\n")) {
                $newlineFound = true;
                break;
            }
        }

        // ignore one-line files
        if (!$newlineFound) {
            return;
        }

        $token = $tokens[0];

        if (false === strpos($token->getContent(), "\n")) {
            $token->setContent(rtrim($token->getContent()).$lineEnding);
        }

        if (!$tokens[1]->isWhitespace() && false === strpos($tokens[1]->getContent(), "\n")) {
            $tokens->insertAt(1, new Token(array(T_WHITESPACE, $lineEnding)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ensure there is no code on the same line as the PHP open tag and it is followed by a blankline.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run before the NoBlankLinesBeforeNamespaceFixer
        return 1;
    }
}
