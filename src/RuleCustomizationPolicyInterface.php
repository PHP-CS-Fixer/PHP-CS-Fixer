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

namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface RuleCustomizationPolicyInterface
{
    /**
     * Customize fixers for given files.
     *
     * Array keys are fixer names, values are closures that will be invoked before applying the fixer.
     * The closure receives the fixer and the file as arguments and must return one of:
     * - the same fixer instance to apply it as is
     * - a new fixer instance of the same class as the received one (use clone!) to apply a customized version of the fixer
     * - null to skip applying the fixer to the file
     *
     * When PHP-CS-Fixer is about to start fixing files, it will check that the currently applied fixers include at least
     * all the fixers for which customizers are defined. If a customizer is defined for a fixer that is not currently applied,
     * an exception will be thrown.
     * This ensures that customizers are actually used for expected fixers, which may be replaced by newer fixers in newer versions of PHP-CS-Fixer.
     *
     * @example
     * ```
     * [
     *     'foo_fixer_name' => static function (FixerInterface $fixer, \SplFileInfo $file): ?FixerInterface {
     *         if (str_contains($file->getFilename(), 'bar')) {
     *             // skip applying the fixer to files with "bar" in the name
     *             return null;
     *         }
     *         if (str_contains($file->getFilename(), 'baz')) {
     *             // apply a customized version of the fixer to files with "baz" in the name
     *             $customizedFixer = clone $fixer;
     *             $customizedFixer->configure([
     *                 'some_option' => false,
     *             ]);
     *            return $customizedFixer;
     *        }
     *        // apply the fixer as is to other files
     *        return $fixer;
     *     },
     * ]
     * ```
     *
     * @return array<string, \Closure(FixerInterface, \SplFileInfo): ?FixerInterface>
     */
    public function getRuleCustomizers(): array;
}
