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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * Fixer for part of the rules defined in PSR2 ¶4.1 Extends and Implements and PSR12 ¶8. Anonymous Classes.
 */
final class ClassDefinitionFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Whitespace around the keywords of a class, trait, enum or interfaces definition should be one space.',
            [
                new CodeSample(
                    '<?php

class  Foo  extends  Bar  implements  Baz,  BarBaz
{
}

final  class  Foo  extends  Bar  implements  Baz,  BarBaz
{
}

trait  Foo
{
}

$foo = new  class  extends  Bar  implements  Baz,  BarBaz {};
'
                ),
                new CodeSample(
                    '<?php

class Foo
extends Bar
implements Baz, BarBaz
{}
',
                    ['single_line' => true]
                ),
                new CodeSample(
                    '<?php

class Foo
extends Bar
implements Baz
{}
',
                    ['single_item_single_line' => true]
                ),
                new CodeSample(
                    '<?php

interface Bar extends
    Bar, BarBaz, FooBarBaz
{}
',
                    ['multi_line_extends_each_single_line' => true]
                ),
                new CodeSample(
                    '<?php
$foo = new class(){};
',
                    ['space_before_parenthesis' => true]
                ),
                new CodeSample(
                    "<?php\n\$foo = new class(\n    \$bar,\n    \$baz\n) {};\n",
                    ['inline_constructor_arguments' => true]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, SingleLineEmptyBodyFixer.
     * Must run after NewWithBracesFixer.
     */
    public function getPriority(): int
    {
        return 36;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // -4, one for count to index, 3 because min. of tokens for a classy location.
        for ($index = $tokens->getSize() - 4; $index > 0; --$index) {
            if ($tokens[$index]->isClassy()) {
                $this->fixClassyDefinition($tokens, $index);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('multi_line_extends_each_single_line', 'Whether definitions should be multiline.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('single_item_single_line', 'Whether definitions should be single line when including a single item.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('single_line', 'Whether definitions should be single line.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('space_before_parenthesis', 'Whether there should be a single space after the parenthesis of anonymous class (PSR12) or not.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('inline_constructor_arguments', 'Whether constructor argument list in anonymous classes should be single line.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * @param int $classyIndex Class definition token start index
     */
    private function fixClassyDefinition(Tokens $tokens, int $classyIndex): void
    {
        $classDefInfo = $this->getClassyDefinitionInfo($tokens, $classyIndex);

        // PSR2 4.1 Lists of implements MAY be split across multiple lines, where each subsequent line is indented once.
        // When doing so, the first item in the list MUST be on the next line, and there MUST be only one interface per line.

        if (false !== $classDefInfo['implements']) {
            $classDefInfo['implements'] = $this->fixClassyDefinitionImplements(
                $tokens,
                $classDefInfo['open'],
                $classDefInfo['implements']
            );
        }

        if (false !== $classDefInfo['extends']) {
            $classDefInfo['extends'] = $this->fixClassyDefinitionExtends(
                $tokens,
                false === $classDefInfo['implements'] ? $classDefInfo['open'] : $classDefInfo['implements']['start'],
                $classDefInfo['extends']
            );
        }

        // PSR2: class definition open curly brace must go on a new line.
        // PSR12: anonymous class curly brace on same line if not multi line implements.

        $classDefInfo['open'] = $this->fixClassyDefinitionOpenSpacing($tokens, $classDefInfo);

        if ($classDefInfo['implements']) {
            $end = $classDefInfo['implements']['start'];
        } elseif ($classDefInfo['extends']) {
            $end = $classDefInfo['extends']['start'];
        } else {
            $end = $tokens->getPrevNonWhitespace($classDefInfo['open']);
        }

        if ($classDefInfo['anonymousClass'] && !$this->configuration['inline_constructor_arguments']) {
            if (!$tokens[$end]->equals(')')) { // anonymous class with `extends` and/or `implements`
                $start = $tokens->getPrevMeaningfulToken($end);
                $this->makeClassyDefinitionSingleLine($tokens, $start, $end);
                $end = $start;
            }

            if ($tokens[$end]->equals(')')) { // skip constructor arguments of anonymous class
                $end = $tokens->findBlockStart(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $end);
            }
        }

        // 4.1 The extends and implements keywords MUST be declared on the same line as the class name.
        $this->makeClassyDefinitionSingleLine($tokens, $classDefInfo['start'], $end);

        $this->sortClassModifiers($tokens, $classDefInfo);
    }

    private function fixClassyDefinitionExtends(Tokens $tokens, int $classOpenIndex, array $classExtendsInfo): array
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if (true === $this->configuration['single_line'] || false === $classExtendsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
            $classExtendsInfo['multiLine'] = false;
        } elseif (true === $this->configuration['single_item_single_line'] && 1 === $classExtendsInfo['numberOfExtends']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
            $classExtendsInfo['multiLine'] = false;
        } elseif (true === $this->configuration['multi_line_extends_each_single_line'] && $classExtendsInfo['multiLine']) {
            $this->makeClassyInheritancePartMultiLine($tokens, $classExtendsInfo['start'], $endIndex);
            $classExtendsInfo['multiLine'] = true;
        }

        return $classExtendsInfo;
    }

    private function fixClassyDefinitionImplements(Tokens $tokens, int $classOpenIndex, array $classImplementsInfo): array
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if (true === $this->configuration['single_line'] || false === $classImplementsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
            $classImplementsInfo['multiLine'] = false;
        } elseif (true === $this->configuration['single_item_single_line'] && 1 === $classImplementsInfo['numberOfImplements']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
            $classImplementsInfo['multiLine'] = false;
        } else {
            $this->makeClassyInheritancePartMultiLine($tokens, $classImplementsInfo['start'], $endIndex);
            $classImplementsInfo['multiLine'] = true;
        }

        return $classImplementsInfo;
    }

    private function fixClassyDefinitionOpenSpacing(Tokens $tokens, array $classDefInfo): int
    {
        if ($classDefInfo['anonymousClass']) {
            if (false !== $classDefInfo['implements']) {
                $spacing = $classDefInfo['implements']['multiLine'] ? $this->whitespacesConfig->getLineEnding() : ' ';
            } elseif (false !== $classDefInfo['extends']) {
                $spacing = $classDefInfo['extends']['multiLine'] ? $this->whitespacesConfig->getLineEnding() : ' ';
            } else {
                $spacing = ' ';
            }
        } else {
            $spacing = $this->whitespacesConfig->getLineEnding();
        }

        $openIndex = $tokens->getNextTokenOfKind($classDefInfo['classy'], ['{']);

        if (' ' !== $spacing && str_contains($tokens[$openIndex - 1]->getContent(), "\n")) {
            return $openIndex;
        }

        if ($tokens[$openIndex - 1]->isWhitespace()) {
            if (' ' !== $spacing || !$tokens[$tokens->getPrevNonWhitespace($openIndex - 1)]->isComment()) {
                $tokens[$openIndex - 1] = new Token([T_WHITESPACE, $spacing]);
            }

            return $openIndex;
        }

        $tokens->insertAt($openIndex, new Token([T_WHITESPACE, $spacing]));

        return $openIndex + 1;
    }

    /**
     * @return array{
     *     start: int,
     *     classy: int,
     *     open: int,
     *     extends: false|array{start: int, numberOfExtends: int, multiLine: bool},
     *     implements: false|array{start: int, numberOfImplements: int, multiLine: bool},
     *     anonymousClass: bool,
     *     final: false|int,
     *     abstract: false|int,
     *     readonly: false|int,
     * }
     */
    private function getClassyDefinitionInfo(Tokens $tokens, int $classyIndex): array
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $openIndex = $tokens->getNextTokenOfKind($classyIndex, ['{']);
        $def = [
            'classy' => $classyIndex,
            'open' => $openIndex,
            'extends' => false,
            'implements' => false,
            'anonymousClass' => false,
            'final' => false,
            'abstract' => false,
            'readonly' => false,
        ];

        if (!$tokens[$classyIndex]->isGivenKind(T_TRAIT)) {
            $extends = $tokens->findGivenKind(T_EXTENDS, $classyIndex, $openIndex);
            $def['extends'] = \count($extends) ? $this->getClassyInheritanceInfo($tokens, key($extends), 'numberOfExtends') : false;

            if (!$tokens[$classyIndex]->isGivenKind(T_INTERFACE)) {
                $implements = $tokens->findGivenKind(T_IMPLEMENTS, $classyIndex, $openIndex);
                $def['implements'] = \count($implements) ? $this->getClassyInheritanceInfo($tokens, key($implements), 'numberOfImplements') : false;
                $def['anonymousClass'] = $tokensAnalyzer->isAnonymousClass($classyIndex);
            }
        }

        if ($def['anonymousClass']) {
            $startIndex = $tokens->getPrevMeaningfulToken($classyIndex); // go to "new" for anonymous class
        } else {
            $modifiers = $tokensAnalyzer->getClassyModifiers($classyIndex);
            $startIndex = $classyIndex;

            foreach (['final', 'abstract', 'readonly'] as $modifier) {
                if (isset($modifiers[$modifier])) {
                    $def[$modifier] = $modifiers[$modifier];
                    $startIndex = min($startIndex, $modifiers[$modifier]);
                } else {
                    $def[$modifier] = false;
                }
            }
        }

        $def['start'] = $startIndex;

        return $def;
    }

    private function getClassyInheritanceInfo(Tokens $tokens, int $startIndex, string $label): array
    {
        $implementsInfo = ['start' => $startIndex, $label => 1, 'multiLine' => false];
        ++$startIndex;
        $endIndex = $tokens->getNextTokenOfKind($startIndex, ['{', [T_IMPLEMENTS], [T_EXTENDS]]);
        $endIndex = $tokens[$endIndex]->equals('{') ? $tokens->getPrevNonWhitespace($endIndex) : $endIndex;

        for ($i = $startIndex; $i < $endIndex; ++$i) {
            if ($tokens[$i]->equals(',')) {
                ++$implementsInfo[$label];

                continue;
            }

            if (!$implementsInfo['multiLine'] && str_contains($tokens[$i]->getContent(), "\n")) {
                $implementsInfo['multiLine'] = true;
            }
        }

        return $implementsInfo;
    }

    private function makeClassyDefinitionSingleLine(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        for ($i = $endIndex; $i >= $startIndex; --$i) {
            if ($tokens[$i]->isWhitespace()) {
                if ($tokens[$i - 1]->isComment() || $tokens[$i + 1]->isComment()) {
                    $content = $tokens[$i - 1]->getContent();

                    if (!('#' === $content || str_starts_with($content, '//'))) {
                        $content = $tokens[$i + 1]->getContent();

                        if (!('#' === $content || str_starts_with($content, '//'))) {
                            $tokens[$i] = new Token([T_WHITESPACE, ' ']);
                        }
                    }

                    continue;
                }

                if ($tokens[$i - 1]->isGivenKind(T_CLASS) && $tokens[$i + 1]->equals('(')) {
                    if (true === $this->configuration['space_before_parenthesis']) {
                        $tokens[$i] = new Token([T_WHITESPACE, ' ']);
                    } else {
                        $tokens->clearAt($i);
                    }

                    continue;
                }

                if (!$tokens[$i - 1]->equals(',') && $tokens[$i + 1]->equalsAny([',', ')']) || $tokens[$i - 1]->equals('(')) {
                    $tokens->clearAt($i);

                    continue;
                }

                $tokens[$i] = new Token([T_WHITESPACE, ' ']);

                continue;
            }

            if ($tokens[$i]->equals(',') && !$tokens[$i + 1]->isWhitespace()) {
                $tokens->insertAt($i + 1, new Token([T_WHITESPACE, ' ']));

                continue;
            }

            if (true === $this->configuration['space_before_parenthesis'] && $tokens[$i]->isGivenKind(T_CLASS) && !$tokens[$i + 1]->isWhitespace()) {
                $tokens->insertAt($i + 1, new Token([T_WHITESPACE, ' ']));

                continue;
            }

            if (!$tokens[$i]->isComment()) {
                continue;
            }

            if (!$tokens[$i + 1]->isWhitespace() && !$tokens[$i + 1]->isComment() && !str_contains($tokens[$i]->getContent(), "\n")) {
                $tokens->insertAt($i + 1, new Token([T_WHITESPACE, ' ']));
            }

            if (!$tokens[$i - 1]->isWhitespace() && !$tokens[$i - 1]->isComment()) {
                $tokens->insertAt($i, new Token([T_WHITESPACE, ' ']));
            }
        }
    }

    private function makeClassyInheritancePartMultiLine(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        for ($i = $endIndex; $i > $startIndex; --$i) {
            $previousInterfaceImplementingIndex = $tokens->getPrevTokenOfKind($i, [',', [T_IMPLEMENTS], [T_EXTENDS]]);
            $breakAtIndex = $tokens->getNextMeaningfulToken($previousInterfaceImplementingIndex);

            // make the part of a ',' or 'implements' single line
            $this->makeClassyDefinitionSingleLine(
                $tokens,
                $breakAtIndex,
                $i
            );

            // make sure the part is on its own line
            $isOnOwnLine = false;

            for ($j = $breakAtIndex; $j > $previousInterfaceImplementingIndex; --$j) {
                if (str_contains($tokens[$j]->getContent(), "\n")) {
                    $isOnOwnLine = true;

                    break;
                }
            }

            if (!$isOnOwnLine) {
                if ($tokens[$breakAtIndex - 1]->isWhitespace()) {
                    $tokens[$breakAtIndex - 1] = new Token([
                        T_WHITESPACE,
                        $this->whitespacesConfig->getLineEnding().$this->whitespacesConfig->getIndent(),
                    ]);
                } else {
                    $tokens->insertAt($breakAtIndex, new Token([T_WHITESPACE, $this->whitespacesConfig->getLineEnding().$this->whitespacesConfig->getIndent()]));
                }
            }

            $i = $previousInterfaceImplementingIndex + 1;
        }
    }

    /**
     * @param array{
     *     final: false|int,
     *     abstract: false|int,
     *     readonly: false|int,
     * } $classDefInfo
     */
    private function sortClassModifiers(Tokens $tokens, array $classDefInfo): void
    {
        if (false === $classDefInfo['readonly']) {
            return;
        }

        $readonlyIndex = $classDefInfo['readonly'];

        foreach (['final', 'abstract'] as $accessModifier) {
            if (false === $classDefInfo[$accessModifier] || $classDefInfo[$accessModifier] < $readonlyIndex) {
                continue;
            }

            $accessModifierIndex = $classDefInfo[$accessModifier];

            /** @var Token $readonlyToken */
            $readonlyToken = clone $tokens[$readonlyIndex];

            /** @var Token $accessToken */
            $accessToken = clone $tokens[$accessModifierIndex];

            $tokens[$readonlyIndex] = $accessToken;
            $tokens[$accessModifierIndex] = $readonlyToken;

            break;
        }
    }
}
