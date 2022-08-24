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

namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTypeTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Transform `|` operator into CT::T_TYPE_ALTERNATION in `function foo(Type1 | Type2 $x) {`
 * or `} catch (ExceptionType1 | ExceptionType2 $e) {`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TypeAlternationTransformer extends AbstractTypeTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        // needs to run after ArrayTypehintTransformer, TypeColonTransformer and AttributeTransformer
        return -15;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId(): int
    {
        return 70100;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens, Token $token, int $index): void
    {
        $this->doProcess($tokens, $index, '|');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokens(): array
    {
        return [CT::T_TYPE_ALTERNATION];
    }

    protected function replaceToken(Tokens $tokens, int $index): void
    {
        $tokens[$index] = new Token([CT::T_TYPE_ALTERNATION, '|']);
    }
}
