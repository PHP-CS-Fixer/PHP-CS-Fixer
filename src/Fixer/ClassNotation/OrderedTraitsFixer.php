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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class OrderedTraitsFixer extends AbstractOrderFixer implements ConfigurableFixerInterface
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
            'Trait `use` statements must be sorted alphabetically or by length.',
            [
                new CodeSample("<?php class Foo { \nuse Z; use A; }\n"),
                new CodeSample(
                    "<?php class Foo { \nuse Aaa; use A; use Aa; }\n",
                    [self::OPTION_ORDER => AbstractOrderFixer::SORT_ORDER_LENGTH]
                ),
                new CodeSample(
                    "<?php class Foo { \nuse Aaa; use A; use Aa; }\n",
                    [
                        self::OPTION_ORDER => AbstractOrderFixer::SORT_ORDER_LENGTH,
                        AbstractOrderFixer::OPTION_DIRECTION => AbstractOrderFixer::DIRECTION_DESCEND,
                    ]
                ),
                new CodeSample(
                    "<?php class Foo { \nuse Aaa; use AA; }\n",
                    [
                        AbstractOrderFixer::OPTION_CASE_SENSITIVE => true,
                    ]
                ),
            ],
            null,
            'Risky when depending on order of the imports.'
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(CT::T_USE_TRAIT);
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_ORDER, 'How the traits should be ordered.'))
                ->setAllowedValues(self::SUPPORTED_SORT_ORDER_OPTIONS)
                ->setDefault(AbstractOrderFixer::SORT_ORDER_ALPHA)
                ->getOption(),
            (new FixerOptionBuilder(AbstractOrderFixer::OPTION_DIRECTION, 'Which direction the traits should be ordered by.'))
                ->setAllowedValues(AbstractOrderFixer::SUPPORTED_DIRECTION_OPTIONS)
                ->setDefault(AbstractOrderFixer::DIRECTION_ASCEND)
                ->getOption(),
            (new FixerOptionBuilder(AbstractOrderFixer::OPTION_CASE_SENSITIVE, 'Whether the sorting should be case sensitive.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->findUseStatementsGroups($tokens) as $uses) {
            $this->sortUseStatements($tokens, $uses);
        }
    }

    protected function getSortOrderOptionName(): string
    {
        return self::OPTION_ORDER;
    }

    /**
     * @return iterable<array<int, Tokens>>
     */
    private function findUseStatementsGroups(Tokens $tokens): iterable
    {
        $uses = [];

        for ($index = 1, $max = \count($tokens); $index < $max; ++$index) {
            $token = $tokens[$index];

            if ($token->isWhitespace() || $token->isComment()) {
                continue;
            }

            if (!$token->isGivenKind(CT::T_USE_TRAIT)) {
                if (\count($uses) > 0) {
                    yield $uses;

                    $uses = [];
                }

                continue;
            }

            $startIndex = $tokens->getNextNonWhitespace($tokens->getPrevMeaningfulToken($index));
            $endIndex = $tokens->getNextTokenOfKind($index, [';', '{']);

            if ($tokens[$endIndex]->equals('{')) {
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $endIndex);
            }

            $use = [];

            for ($i = $startIndex; $i <= $endIndex; ++$i) {
                $use[] = $tokens[$i];
            }

            $uses[$startIndex] = Tokens::fromArray($use);

            $index = $endIndex;
        }
    }

    /**
     * @param array<int, Tokens> $uses
     */
    private function sortUseStatements(Tokens $tokens, array $uses): void
    {
        foreach ($uses as $use) {
            $this->sortMultipleTraitsInStatement($use);
        }

        $this->sort($tokens, $uses);
    }

    private function sortMultipleTraitsInStatement(Tokens $use): void
    {
        $traits = [];
        $indexOfName = null;
        $name = [];

        for ($index = 0, $max = \count($use); $index < $max; ++$index) {
            $token = $use[$index];

            if ($token->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
                $name[] = $token;

                if (null === $indexOfName) {
                    $indexOfName = $index;
                }

                continue;
            }

            if ($token->equalsAny([',', ';', '{'])) {
                $traits[$indexOfName] = Tokens::fromArray($name);

                $name = [];
                $indexOfName = null;
            }

            if ($token->equals('{')) {
                $index = $use->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
            }
        }

        $this->sort($use, $traits);
    }

    /**
     * @param array<int, Tokens> $elements
     */
    private function sort(Tokens $tokens, array $elements): void
    {
        $toTraitName = static function (Tokens $use): string {
            $string = '';

            foreach ($use as $token) {
                if ($token->equalsAny([';', '{'])) {
                    break;
                }

                if ($token->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
                    $string .= $token->getContent();
                }
            }

            return ltrim($string, '\\');
        };

        $sortedElements = $elements;
        uasort($sortedElements, function (Tokens $useA, Tokens $useB) use ($toTraitName): int {
            return $this->getScoreWithSortAlgorithm($toTraitName($useA), $toTraitName($useB));
        });

        $sortedElements = array_combine(
            array_keys($elements),
            array_values($sortedElements)
        );

        foreach (array_reverse($sortedElements, true) as $index => $tokensToInsert) {
            $tokens->overrideRange(
                $index,
                $index + \count($elements[$index]) - 1,
                $tokensToInsert
            );
        }
    }
}
