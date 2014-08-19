<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\Token;
use Symfony\CS\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class PhpClosingTagFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $kinds = $tokens->findGivenKind(array(T_OPEN_TAG, T_CLOSE_TAG, T_INLINE_HTML, ));

        // leave code intact if there is:
        // - any T_INLINE_HTML code
        // - several opening tags
        if (count($kinds[T_INLINE_HTML]) || count($kinds[T_OPEN_TAG]) > 1) {
            return $content;
        }

        foreach (array_reverse($kinds[T_CLOSE_TAG], true) as $index => $token) {
            $tokens->removeLeadingWhitespace($index);
            $token->clear();

            $prevIndex = null;
            $prevToken = $tokens->getPrevNonWhitespace($index, array(), $prevIndex);

            if (null !== $prevToken->id || ';' !== $prevToken->content) {
                $tokens->insertAt($prevIndex + 1, new Token(';'));
            }
        }

        return $tokens->generateCode();
    }

    public function getLevel()
    {
        // defined in PSR-2 2.2
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        // should be run before the ShortTagFixer
        return 5;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' === pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'php_closing_tag';
    }

    public function getDescription()
    {
        return 'The closing ?> tag MUST be omitted from files containing only PHP.';
    }
}
