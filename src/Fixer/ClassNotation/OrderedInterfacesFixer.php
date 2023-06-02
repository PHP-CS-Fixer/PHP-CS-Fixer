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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractOrderFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 */
final class OrderedInterfacesFixer extends AbstractOrderFixer implements ConfigurableFixerInterface
{
    /** @internal */
    private const OPTION_ORDER = 'order';

    /**
     * Array of supported sort orders in configuration.
     *
     * @var string[]
     */
    private const SUPPORTED_SORT_ORDER_OPTIONS = [
        AbstractOrderFixer::SORT_ORDER_ALPHA,
        AbstractOrderFixer::SORT_ORDER_LENGTH,
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Orders the interfaces in an `implements` or `interface extends` clause.',
            [
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements Gamma, Alpha, Beta {}\n\ninterface ExampleB extends Gamma, Alpha, Beta {}\n"
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements Gamma, Alpha, Beta {}\n\ninterface ExampleB extends Gamma, Alpha, Beta {}\n",
                    [AbstractOrderFixer::OPTION_DIRECTION => AbstractOrderFixer::DIRECTION_DESCEND]
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements MuchLonger, Short, Longer {}\n\ninterface ExampleB extends MuchLonger, Short, Longer {}\n",
                    [self::OPTION_ORDER => AbstractOrderFixer::SORT_ORDER_LENGTH]
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements MuchLonger, Short, Longer {}\n\ninterface ExampleB extends MuchLonger, Short, Longer {}\n",
                    [
                        self::OPTION_ORDER => AbstractOrderFixer::SORT_ORDER_LENGTH,
                        AbstractOrderFixer::OPTION_DIRECTION => AbstractOrderFixer::DIRECTION_DESCEND,
                    ]
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements IgnorecaseB, IgNoReCaSeA, IgnoreCaseC {}\n\ninterface ExampleB extends IgnorecaseB, IgNoReCaSeA, IgnoreCaseC {}\n",
                    [
                        self::OPTION_ORDER => AbstractOrderFixer::SORT_ORDER_ALPHA,
                    ]
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements Casesensitivea, CaseSensitiveA, CasesensitiveA {}\n\ninterface ExampleB extends Casesensitivea, CaseSensitiveA, CasesensitiveA {}\n",
                    [
                        self::OPTION_ORDER => AbstractOrderFixer::SORT_ORDER_ALPHA,
                        AbstractOrderFixer::OPTION_CASE_SENSITIVE => true,
                    ]
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_IMPLEMENTS)
            || $tokens->isAllTokenKindsFound([T_INTERFACE, T_EXTENDS]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_IMPLEMENTS)) {
                if (!$token->isGivenKind(T_EXTENDS)) {
                    continue;
                }

                $nameTokenIndex = $tokens->getPrevMeaningfulToken($index);
                $interfaceTokenIndex = $tokens->getPrevMeaningfulToken($nameTokenIndex);
                $interfaceToken = $tokens[$interfaceTokenIndex];

                if (!$interfaceToken->isGivenKind(T_INTERFACE)) {
                    continue;
                }
            }

            $implementsStart = $index + 1;
            $implementsEnd = $tokens->getPrevNonWhitespace($tokens->getNextTokenOfKind($implementsStart, ['{']));

            $interfaces = $this->getInterfaces($tokens, $implementsStart, $implementsEnd);

            if (1 === \count($interfaces)) {
                continue;
            }

            foreach ($interfaces as $interfaceIndex => $interface) {
                $interfaceTokens = Tokens::fromArray($interface, false);
                $normalized = '';
                $actualInterfaceIndex = $interfaceTokens->getNextMeaningfulToken(-1);

                while ($interfaceTokens->offsetExists($actualInterfaceIndex)) {
                    $token = $interfaceTokens[$actualInterfaceIndex];

                    if ($token->isComment() || $token->isWhitespace()) {
                        break;
                    }

                    $normalized .= str_replace('\\', ' ', $token->getContent());
                    ++$actualInterfaceIndex;
                }

                $interfaces[$interfaceIndex] = [
                    'tokens' => $interface,
                    'normalized' => $normalized,
                    'originalIndex' => $interfaceIndex,
                ];
            }

            usort($interfaces, function (array $first, array $second): int {
                return $this->sortElementsWithSortAlgorithm($first['normalized'], $second['normalized']);
            });

            $changed = false;

            foreach ($interfaces as $interfaceIndex => $interface) {
                if ($interface['originalIndex'] !== $interfaceIndex) {
                    $changed = true;

                    break;
                }
            }

            if (!$changed) {
                continue;
            }

            $newTokens = array_shift($interfaces)['tokens'];

            foreach ($interfaces as $interface) {
                array_push($newTokens, new Token(','), ...$interface['tokens']);
            }

            $tokens->overrideRange($implementsStart, $implementsEnd, $newTokens);
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_ORDER, 'How the interfaces should be ordered.'))
                ->setAllowedValues(self::SUPPORTED_SORT_ORDER_OPTIONS)
                ->setDefault(AbstractOrderFixer::SORT_ORDER_ALPHA)
                ->getOption(),
            (new FixerOptionBuilder(AbstractOrderFixer::OPTION_DIRECTION, 'Which direction the interfaces should be ordered.'))
                ->setAllowedValues(AbstractOrderFixer::SUPPORTED_DIRECTION_OPTIONS)
                ->setDefault(AbstractOrderFixer::DIRECTION_ASCEND)
                ->getOption(),
            (new FixerOptionBuilder(AbstractOrderFixer::OPTION_CASE_SENSITIVE, 'Whether the sorting should be case sensitive.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function getSortOrderOptionName(): string
    {
        return self::OPTION_ORDER;
    }

    /**
     * @return array<int, list<Token>>
     */
    private function getInterfaces(Tokens $tokens, int $implementsStart, int $implementsEnd): array
    {
        $interfaces = [];
        $interfaceIndex = 0;

        for ($i = $implementsStart; $i <= $implementsEnd; ++$i) {
            if ($tokens[$i]->equals(',')) {
                ++$interfaceIndex;
                $interfaces[$interfaceIndex] = [];

                continue;
            }

            $interfaces[$interfaceIndex][] = $tokens[$i];
        }

        return $interfaces;
    }
}
