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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Linter\CachingLinter;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\ProcessLinter;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
trait LinterProviderTrait
{
    private function getLinter(): LinterInterface
    {
        static $linter = null;

        if (null === $linter) {
            $linter = new CachingLinter(
                self::shouldFastLintTestCases()
                    ? new Linter()
                    : new ProcessLinter(),
            );
        }

        return $linter;
    }

    private static function shouldFastLintTestCases(): bool
    {
        $value = getenv('PHP_CS_FIXER_FAST_LINT_TEST_CASES');

        if (false === $value) {
            return true; // not set - default to fast linting
        }

        if ('' === $value) {
            return true; // CI exports the variable as empty string when not configured in the workflow matrix
        }

        return filter_var($value, \FILTER_VALIDATE_BOOLEAN);
    }
}
