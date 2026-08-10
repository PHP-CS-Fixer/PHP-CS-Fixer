<?php

declare(strict_types=1);

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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Your name <your@email.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RemoveCommentsFixer extends AbstractFixer
{
    /**
     * このfixerが何をするものなのか定義する。
     * 説明文やコード例などを持たせ、ドキュメント生成にも使われる。
     */
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Removes comments that are preceded by `;` (semicolon).',
            [
                new CodeSample(
                    "<?php echo 123; /* Comment */\n",
                ),
            ],
        );
    }

    /**
     * 渡されたPHPコードが、このFixerの処理対象になり得るかを素早く判定する場所。
     * 例：「そもそもコメントが一つも存在しないコード」を早々に除外する。
     * このメソッドは大量に呼ばれるので、重い処理をしないことが重要。
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }

    /**
     * 実際にコードを書き換える本体。
     * 最終的にはここでTokensを調べて、条件に一致するコメントを削除する。
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }

            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            if ($prevToken->equals(';')) {
                $tokens->clearAt($index);
            }
        }
    }
}
