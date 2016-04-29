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
 * @author Ceeram <ceeram@cakephp.org>
 */
class NewlineAfterOpenTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        // ignore non-monolithic files
        if (!$tokens->isMonolithicPhp()) {
            return $content;
        }

        // ignore files with short open tag
        if (!$tokens[0]->isGivenKind(T_OPEN_TAG)) {
            return $content;
        }

        $newlineFound = false;
        foreach ($tokens as $token) {
            if ($token->isWhitespace(array('whitespaces' => "\n"))) {
                $newlineFound = true;
                break;
            }
        }

        // ignore one-line files
        if (!$newlineFound) {
            return $content;
        }

        $token = $tokens[0];
        $token->setContent(rtrim($token->getContent())."\n");

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Ensure there is no code on the same line as the PHP open tag.';
    }
}
