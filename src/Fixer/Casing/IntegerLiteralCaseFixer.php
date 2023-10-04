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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * @deprecated in favor of NumericLiteralCaseFixer
 */
final class IntegerLiteralCaseFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Integer literals must be in correct case.',
            [
                new CodeSample(
                    "<?php\n\$foo = 0Xff;\n\$bar = 0B11111111;\n"
                ),
            ]
        );
    }

    public function getSuccessorsNames(): array
    {
        return array_keys($this->proxyFixers);
    }

    protected function createProxyFixers(): array
    {
        $fixer = new NumericLiteralCaseFixer();

        return [$fixer];
    }
}
