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

/**
 * @author Michael Vorisek <https://github.com/mvorisek>
 */
final class HeredocClosingMarkerFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var list<string>
     */
    public const RESERVED_CLOSING_MARKERS = [
        'CSS',
        'DIFF',
        'HTML',
        'JS',
        'JSON',
        'MD',
        'PHP',
        'PYTHON',
        'RST',
        'TS',
        'SQL',
        'XML',
        'YAML',
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Unify `heredoc` or `nowdoc` closing marker.',
            [
                new CodeSample(
                    <<<'EOD'
                        <?php $a = <<<"TEST"
                        Foo
                        TEST;

                        EOD
                ),
                new CodeSample(
                    <<<'EOD'
                        <?php $a = <<<'TEST'
                        Foo
                        TEST;

                        EOD,
                    ['closing_marker' => 'EOF']
                ),
                new CodeSample(
                    <<<'EOD_'
                        <?php $a = <<<EOD
                        Foo
                        EOD;

                        EOD_,
                    ['explicit_heredoc_style' => true]
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
            (new FixerOptionBuilder(
                'reserved_closing_markers',
                'Reserved closing markers to be kept unchanged.'
            ))
                ->setAllowedTypes(['array'])
                ->setDefault(self::RESERVED_CLOSING_MARKERS)
                ->getOption(),
            (new FixerOptionBuilder(
                'explicit_heredoc_style',
                'Whether the closing marker should be wrapped in double quotes.'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $reservedClosingMarkersMap = null;

        $startIndex = null;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_START_HEREDOC)) {
                $startIndex = $index;

                continue;
            }

            if (null !== $startIndex && $token->isGivenKind(T_END_HEREDOC)) {
                $existingClosingMarker = trim($token->getContent());

                if (null === $reservedClosingMarkersMap) {
                    $reservedClosingMarkersMap = [];
                    foreach ($this->configuration['reserved_closing_markers'] as $v) {
                        $reservedClosingMarkersMap[mb_strtoupper($v)] = $v;
                    }
                }

                $existingClosingMarker = mb_strtoupper($existingClosingMarker);
                do {
                    $newClosingMarker = $reservedClosingMarkersMap[$existingClosingMarker] ?? null;
                    if (!str_ends_with($existingClosingMarker, '_')) {
                        break;
                    }
                    $existingClosingMarker = substr($existingClosingMarker, 0, -1);
                } while (null === $newClosingMarker);

                if (null === $newClosingMarker) {
                    $newClosingMarker = $this->configuration['closing_marker'];
                }

                $content = $tokens->generatePartialCode($startIndex + 1, $index - 1);
                while (Preg::match('~(^|[\r\n])\s*'.preg_quote($newClosingMarker, '~').'(?!\w)~', $content)) {
                    $newClosingMarker .= '_';
                }

                [$tokens[$startIndex], $tokens[$index]] = $this->convertClosingMarker($tokens[$startIndex], $token, $newClosingMarker);

                $startIndex = null;

                continue;
            }
        }
    }

    /**
     * @return array{Token, Token}
     */
    private function convertClosingMarker(Token $startToken, Token $endToken, string $newClosingMarker): array
    {
        $isNowdoc = str_contains($startToken->getContent(), '\'');

        $markerQuote = $isNowdoc
            ? '\''
            : (true === $this->configuration['explicit_heredoc_style'] ? '"' : '');

        return [new Token([
            $startToken->getId(),
            Preg::replace('/<<<\h*\K["\']?[^\s"\']+["\']?/', $markerQuote.$newClosingMarker.$markerQuote, $startToken->getContent()),
        ]), new Token([
            $endToken->getId(),
            Preg::replace('/\S+/', $newClosingMarker, $endToken->getContent()),
        ])];
    }
}
