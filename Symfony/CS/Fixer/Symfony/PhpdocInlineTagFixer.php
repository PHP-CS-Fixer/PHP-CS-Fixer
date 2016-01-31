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
 * Fix inline tags and make inheritdoc tag always inline.
 */
final class PhpdocInlineTagFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $content = $token->getContent();

            // Move `@` inside tag, for example @{tag} -> {@tag}, replace multiple curly brackets,
            // remove spaces between '{' and '@', remove 's' at the end of tag.
            // Make sure the tags are written in lower case, remove white space between end
            // of text and closing bracket and between the tag and inline comment.
            $content = preg_replace_callback(
                '#(?:@{+|{+[ \t]*@)[ \t]*(example|id|internal|inheritdoc|link|source|toc|tutorial)s?([^}]*)(?:}+)#i',
                function (array $matches) {
                    $doc = trim($matches[2]);

                    if ('' === $doc) {
                        return '{@'.strtolower($matches[1]).'}';
                    }

                    return '{@'.strtolower($matches[1]).' '.$doc.'}';
                },
                $content
            );

            // Always make inheritdoc inline using with '{' '}' when needed, remove trailing 's',
            // make sure lowercase.
            $content = preg_replace(
                '#(?<!{)@inheritdocs?(?!})#i',
                '{@inheritdoc}',
                $content
            );

            $token->setContent($content);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Fix phpdoc inline tags, make inheritdoc always inline.';
    }
}
