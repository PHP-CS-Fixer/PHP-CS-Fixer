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
final class LongToShorthandOperatorForComplexTargetsFixer extends AbstractLongToShorthandOperatorFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Shorthand notation for operators should be used if possible, also for non-plain-variable assignment targets (member access, offsets, ...).',
            [
                new CodeSample("<?php\n\$this->value = \$this->value + 1;\n\$numbers[0] = \$numbers[0] * 2;\n"),
            ],
            null,
            'Risky, because the target is evaluated once instead of twice, which changes behaviour when evaluating it has a side effect '
            .'(e.g. `$a[f()] = $a[f()] + 1;` calls `f()` once after the change) or invokes a magic accessor '
            .'(e.g. `$obj->m->x` when `m` is a `__get`); and because an assign-op on a string offset '
            .'(e.g. `$text[0] = $text[0] & "\x7F";`) fails at runtime with "Cannot use assign-op operators with string offsets".',
        );
    }

    public function isRisky(): bool
    {
        return true;
    }

    /**
     * The complement of the non-risky fixer: everything that is not a plain
     * variable target (member access, offsets, variable-variables, ...).
     *
     * @param array{start: int, end: int} $assignRange
     */
    protected function isAssignTargetCandidate(Tokens $tokens, array $assignRange): bool
    {
        return !$this->targetIsPlainVariable($tokens, $assignRange);
    }
}
