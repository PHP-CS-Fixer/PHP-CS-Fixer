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

namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class HeredocClosingMarkerFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Unify `heredoc` or `nowdoc` closing marker where possible.',
            [
                new CodeSample(
                    <<<'EOF'
                        <?php $a = <<<"TEST"
                        Foo
                        TEST;

                        EOF
                ),
                new CodeSample(
                    <<<'EOF'
                        <?php $a = <<<"TEST"
                        Foo
                        TEST;

                        EOF,
                    ['closing_marker' => 'EOF']
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_START_HEREDOC);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                'closing_marker',
                'Preferred closing marker.'
            ))
                ->setAllowedTypes(['string'])
                ->setDefault('EOD')
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $closingMarkerRegex = '~[\r\n]\s*'.preg_quote($this->configuration['closing_marker'], '~').'(?!\w)~';

        $startIndex = null;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_START_HEREDOC)) {
                $startIndex = $index;

                continue;
            }

            if (null !== $startIndex && $token->isGivenKind(T_END_HEREDOC)) {
                $content = $tokens->generatePartialCode($startIndex, $index);
                if (!Preg::match($closingMarkerRegex, $content)) {
                    [$tokens[$startIndex], $tokens[$index]] = $this->convertClosingMarker($tokens[$startIndex], $token);
                }

                continue;
            }
        }
    }

    /**
     * @return array{Token, Token}
     */
    private function convertClosingMarker(Token $startToken, Token $endToken): array
    {
        $preferredClosingMarker = $this->configuration['closing_marker'];

        return [new Token([
            $startToken->getId(),
            Preg::replace('/<<<\h*["\']?\K[^\s"\']+/', $preferredClosingMarker, $startToken->getContent()),
        ]), new Token([
            $endToken->getId(),
            Preg::replace('/[^\s"\']+/', $preferredClosingMarker, $endToken->getContent()),
        ])];
    }
}
