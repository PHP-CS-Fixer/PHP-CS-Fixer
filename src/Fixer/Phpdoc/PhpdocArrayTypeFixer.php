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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractPhpdocTypesFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpdocArrayTypeFixer extends AbstractPhpdocTypesFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function isRisky(): bool
    {
        return true;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'PHPDoc `array<T>` type must be used instead of `T[]`.',
            [
                new CodeSample(<<<'PHP'
                    <?php
                    /**
                     * @param int[] $x
                     * @param string[][] $y
                     */

                    PHP),
            ],
            null,
            'Risky when `array` key should be present, but is missing.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer, PhpdocListTypeFixer, PhpdocTypesOrderFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return 2;
    }

    protected function normalize(string $type): string
    {
        if (Preg::match('/^\??\s*[\'"]/', $type)) {
            return $type;
        }

        $prefix = '';
        if (str_starts_with($type, '?')) {
            $prefix = '?';
            $type = substr($type, 1);
        }

        return $prefix.Preg::replaceCallback(
            '/^(.+?)((?:\h*\[\h*\])+)$/',
            static function (array $matches): string {
                $level = substr_count($matches[2], '[');

                return str_repeat('array<', $level).$matches[1].str_repeat('>', $level);
            },
            $type,
        );
    }
}
