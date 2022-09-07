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

use PhpCsFixer\AbstractFixer;
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
final class OrderedInterfacesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /** @internal */
    public const OPTION_DIRECTION = 'direction';

    /** @internal */
    public const OPTION_ORDER = 'order';

    /** @internal */
    public const DIRECTION_ASCEND = 'ascend';

    /** @internal */
    public const DIRECTION_DESCEND = 'descend';

    /** @internal */
    public const ORDER_ALPHA = 'alpha';

    /** @internal */
    public const ORDER_LENGTH = 'length';

    /**
     * Array of supported directions in configuration.
     *
     * @var string[]
     */
    private const SUPPORTED_DIRECTION_OPTIONS = [
        self::DIRECTION_ASCEND,
        self::DIRECTION_DESCEND,
    ];

    /**
     * Array of supported orders in configuration.
     *
     * @var string[]
     */
    private const SUPPORTED_ORDER_OPTIONS = [
        self::ORDER_ALPHA,
        self::ORDER_LENGTH,
    ];

    /**
     * {@inheritdoc}
     */
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
                    [self::OPTION_DIRECTION => self::DIRECTION_DESCEND]
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements MuchLonger, Short, Longer {}\n\ninterface ExampleB extends MuchLonger, Short, Longer {}\n",
                    [self::OPTION_ORDER => self::ORDER_LENGTH]
                ),
                new CodeSample(
                    "<?php\n\nfinal class ExampleA implements MuchLonger, Short, Longer {}\n\ninterface ExampleB extends MuchLonger, Short, Longer {}\n",
                    [
                        self::OPTION_ORDER => self::ORDER_LENGTH,
                        self::OPTION_DIRECTION => self::DIRECTION_DESCEND,
                    ]
                ),
            ],
            null,
            "Risky for `implements` when specifying both an interface and its parent interface, because PHP doesn't break on `parent, child` but does on `child, parent`."
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_IMPLEMENTS)
            || $tokens->isAllTokenKindsFound([T_INTERFACE, T_EXTENDS]);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
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
                $score = self::ORDER_LENGTH === $this->configuration[self::OPTION_ORDER]
                    ? \strlen($first['normalized']) - \strlen($second['normalized'])
                    : strcasecmp($first['normalized'], $second['normalized']);

                if (self::DIRECTION_DESCEND === $this->configuration[self::OPTION_DIRECTION]) {
                    $score *= -1;
                }

                return $score;
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

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_ORDER, 'How the interfaces should be ordered'))
                ->setAllowedValues(self::SUPPORTED_ORDER_OPTIONS)
                ->setDefault(self::ORDER_ALPHA)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_DIRECTION, 'Which direction the interfaces should be ordered'))
                ->setAllowedValues(self::SUPPORTED_DIRECTION_OPTIONS)
                ->setDefault(self::DIRECTION_ASCEND)
                ->getOption(),
        ]);
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
