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

namespace PhpCsFixer\Fixer\ArrayNotation;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\Operator\NoLineBreakNearBinaryOperatorFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @deprecated Use `no_line_break_near_binary_operator` with config: ['default_strategy' => null, 'operators' => ['=>' => 'around']]
 *
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoMultilineWhitespaceAroundDoubleArrowFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    public function getSuccessorsNames(): array
    {
        return array_keys($this->proxyFixers);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Operator `=>` should not be surrounded by multi-line whitespaces.',
            [new CodeSample("<?php\n\$a = array(1\n\n=> 2);\n")],
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, MethodArgumentSpaceFixer.
     */
    public function getPriority(): int
    {
        return 31;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_DOUBLE_ARROW);
    }

    protected function createProxyFixers(): array
    {
        $noLineBreakNearBinaryOperatorFixer = new NoLineBreakNearBinaryOperatorFixer();
        $noLineBreakNearBinaryOperatorFixer->configure([
            'default_strategy' => null,
            'operators' => ['=>' => 'around'],
        ]);

        return [
            $noLineBreakNearBinaryOperatorFixer,
        ];
    }
}
