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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\Fixer\AbstractLongToShorthandOperatorFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class LongToShorthandOperatorFixer extends AbstractLongToShorthandOperatorFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Shorthand notation for operators should be used if possible.',
            [
                new CodeSample("<?php\n\$i = \$i + 10;\n"),
            ],
        );
    }

    /**
     * Only a plain variable target is rewritten here; it is the sole case that
     * is guaranteed to preserve behaviour (see the base class). Every other
     * target is left to `long_to_shorthand_operator_for_complex_targets`.
     *
     * @param array{start: int, end: int} $assignRange
     */
    protected function isAssignTargetCandidate(Tokens $tokens, array $assignRange): bool
    {
        return $this->targetIsPlainVariable($tokens, $assignRange);
    }
}
