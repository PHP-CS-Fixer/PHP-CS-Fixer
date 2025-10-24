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

namespace PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @TODO v4: replace DeprecatedRuleSetDefinitionInterface
 */
interface DeprecatedRuleSetDefinitionV4Interface extends RuleSetDefinitionInterface
{
    /**
     * Returns rules/sets to use instead, if any.
     *
     * @return array<string, array<string, mixed>|bool>
     *
     * @phpstan-ignore shipmonk.deadMethod
     */
    public function getSuccessors(): array;

    /**
     * Returns whether successors fully match predecessor, i.e. one can blindly use successors instead of predecessor.
     *
     * @phpstan-ignore shipmonk.deadMethod
     */
    public function getSuccessorsMatchingPredecessor(): bool;
}
