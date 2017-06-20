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

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class StarToSlashCommentFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Converts multi-line comments that have only one line of actual content into single-line comments.',
            [new CodeSample("<?php\n/* first comment */\n\$a = 1;\n/*\n * second comment\n */\n\$b = 2;\n/*\n * third\n * comment\n */\n\$c = 3;")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        foreach ($tokens as $index => $token) {
            $content = $token->getContent();
            $commentContent = substr($content, 2, -2);
            if (!$token->isGivenKind(T_COMMENT) || '/*' !== substr($content, 0, 2) || preg_match('/[^\s\*].*\R.*[^\s\*]/s', $commentContent)) {
                continue;
            }
            $nextTokenIndex = $index + 1;
            if (isset($tokens[$nextTokenIndex])) {
                $nextToken = $tokens[$nextTokenIndex];
                if (false === strpos($nextToken->getContent(), $lineEnding)) {
                    continue;
                }

                $tokens[$nextTokenIndex] = new Token([$nextToken->getId(), ltrim($nextToken->getContent(), " \t")]);
            }

            $content = '//';
            if (preg_match('/[^\s\*]/', $commentContent)) {
                $content = '// '.preg_replace('/[\s\*]*([^\s\*](.+[^\s\*])?)[\s\*]*/', '\1', $commentContent);
            }
            $tokens[$index] = new Token([$token->getId(), $content]);
        }
    }
}
