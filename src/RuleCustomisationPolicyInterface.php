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
interface RuleCustomisationPolicyInterface
{
    /**
     * Customise fixers for given files.
     *
     * Array keys are fixer names, values are closures that will be invoked before applying the fixer to a specific file.
     * The closures receive the file as argument and must return:
     * - true to apply the fixer as is to the file
     * - false to skip applying the fixer to the file
     * - a new fixer instance to apply a customised version of the fixer
     *
     * When PHP-CS-Fixer is about to start fixing files, it will check that the currently applied fixers include at least
     * all the fixers for which customisation rules are defined. If a customiser is defined for a fixer that is not currently applied,
     * an exception will be thrown.
     * This ensures that customisers are actually used for expected fixers, which may be replaced by newer fixers in newer versions of PHP-CS-Fixer.
     *
     * @example
     * ```
     * [
     *     'array_syntax' => static function (\SplFileInfo $file) {
     *         if (str_contains($file->getPathname(), '/tests/')) {
     *             // Disable the fixer for files in /tests/ directory
     *             return false;
     *         }
     *         if (str_contains($file->getPathname(), '/bin/')) {
     *             // For files in /bin/ directory create a new fixer instance with a different configuration
     *             $fixer = new ArraySyntaxFixer();
     *             $fixer->configure(['syntax' => 'long']);
     *             return $fixer;
     *        }
     *        // Keep the default configuration for other files
     *        return true;
     *     },
     * ]
     * ```
     *
     * @return array<string, \Closure(\SplFileInfo): (bool|FixerInterface)>
     */
    public function getRuleCustomisers(): array;
}
