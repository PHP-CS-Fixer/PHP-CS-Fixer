<?php

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
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AliasedFixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ClassAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ClassesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for part of the rules defined in PSR2 ¶4.1 Extends and Implements and PSR12 ¶8. Anonymous Classes.
 *
 * @author SpacePossum
 */
final class ClassDefinitionFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Whitespace around the keywords of a class, trait or interfaces definition should be one space.',
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
'
                ),
                new VersionSpecificCodeSample(
                    '<?php

$foo = new  class  extends  Bar  implements  Baz,  BarBaz {};
',
                    new VersionSpecification(70100)
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
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer.
     */
    public function getPriority()
    {
        return 36;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        // -4, one for count to index, 3 because min. of tokens for a classy location.
        for ($index = $tokens->getSize() - 4; $index > 0; --$index) {
            if ($tokens[$index]->isClassy()) {
                $this->fixClassyDefinition($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new AliasedFixerOptionBuilder(
                new FixerOptionBuilder('multi_line_extends_each_single_line', 'Whether definitions should be multiline.'),
                'multiLineExtendsEachSingleLine'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new AliasedFixerOptionBuilder(
                new FixerOptionBuilder('single_item_single_line', 'Whether definitions should be single line when including a single item.'),
                'singleItemSingleLine'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new AliasedFixerOptionBuilder(
                new FixerOptionBuilder('single_line', 'Whether definitions should be single line.'),
                'singleLine'
            ))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * @param int $classyIndex Class definition token start index
     */
    private function fixClassyDefinition(Tokens $tokens, $classyIndex)
    {
        $classAnalysis = $this->getClassyDefinitionInfo($tokens, $classyIndex);

        // PSR2 4.1 Lists of implements MAY be split across multiple lines, where each subsequent line is indented once.
        // When doing so, the first item in the list MUST be on the next line, and there MUST be only one interface per line.
        if ([] !== $implements = $classAnalysis->getImplements()) {
            $implements = $this->fixClassyDefinitionImplements(
                $tokens,
                $classAnalysis->getOpen(),
                $implements
            );
        }

        if ([] !== $extends = $classAnalysis->getExtends()) {
            $extends = $this->fixClassyDefinitionExtends(
                $tokens,
                [] === $implements ? $classAnalysis->getOpen() : $implements['start'],
                $extends
            );
        }

        // PSR2: class definition open curly brace must go on a new line.
        // PSR12: anonymous class curly brace on same line if not multi line implements.
        $open = $this->fixClassyDefinitionOpenSpacing($tokens, $classAnalysis);

        if ([] !== $implements) {
            $end = $implements['start'];
        } elseif ([] !== $extends) {
            $end = $extends['start'];
        } else {
            $end = $tokens->getPrevNonWhitespace($open);
        }

        // 4.1 The extends and implements keywords MUST be declared on the same line as the class name.
        $this->makeClassyDefinitionSingleLine(
            $tokens,
            true === $classAnalysis->getAnonymous() ? $tokens->getPrevMeaningfulToken($classyIndex) : $classAnalysis->getStart(),
            $end
        );
    }

    /**
     * @param int $classOpenIndex
     *
     * @return array
     */
    private function fixClassyDefinitionExtends(Tokens $tokens, $classOpenIndex, array $classExtendsInfo)
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if ($this->configuration['single_line'] || false === $classExtendsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
            $classExtendsInfo['multiLine'] = false;
        } elseif ($this->configuration['single_item_single_line'] && 1 === $classExtendsInfo['numberOfExtends']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classExtendsInfo['start'], $endIndex);
            $classExtendsInfo['multiLine'] = false;
        } elseif ($this->configuration['multi_line_extends_each_single_line'] && $classExtendsInfo['multiLine']) {
            $this->makeClassyInheritancePartMultiLine($tokens, $classExtendsInfo['start'], $endIndex);
            $classExtendsInfo['multiLine'] = true;
        }

        return $classExtendsInfo;
    }

    /**
     * @param int $classOpenIndex
     *
     * @return array
     */
    private function fixClassyDefinitionImplements(Tokens $tokens, $classOpenIndex, array $classImplementsInfo)
    {
        $endIndex = $tokens->getPrevNonWhitespace($classOpenIndex);

        if ($this->configuration['single_line'] || false === $classImplementsInfo['multiLine']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
            $classImplementsInfo['multiLine'] = false;
        } elseif ($this->configuration['single_item_single_line'] && 1 === $classImplementsInfo['numberOfImplements']) {
            $this->makeClassyDefinitionSingleLine($tokens, $classImplementsInfo['start'], $endIndex);
            $classImplementsInfo['multiLine'] = false;
        } else {
            $this->makeClassyInheritancePartMultiLine($tokens, $classImplementsInfo['start'], $endIndex);
            $classImplementsInfo['multiLine'] = true;
        }

        return $classImplementsInfo;
    }

    /**
     * @return int
     */
    private function fixClassyDefinitionOpenSpacing(Tokens $tokens, ClassAnalysis $classAnalysis)
    {
        if (true === $classAnalysis->getAnonymous()) {
            if ([] !== $implements = $classAnalysis->getImplements()) {
                $spacing = $implements['multiLine'] ? $this->whitespacesConfig->getLineEnding() : ' ';
            } elseif ([] !== $extends = $classAnalysis->getExtends()) {
                $spacing = $extends['multiLine'] ? $this->whitespacesConfig->getLineEnding() : ' ';
            } else {
                $spacing = ' ';
            }
        } else {
            $spacing = $this->whitespacesConfig->getLineEnding();
        }

        $openIndex = $tokens->getNextTokenOfKind($classAnalysis->getClass(), ['{']);
        if (' ' !== $spacing && false !== strpos($tokens[$openIndex - 1]->getContent(), "\n")) {
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
     * @param int $classyIndex
     *
     * @return ClassAnalysis
     */
    private function getClassyDefinitionInfo(Tokens $tokens, $classyIndex)
    {
        return (new ClassesAnalyzer())->getClassDefinition($tokens, $classyIndex);
    }

    /**
     * @param int    $startIndex
     * @param string $label
     *
     * @return array
     */
    private function getClassyInheritanceInfo(Tokens $tokens, $startIndex, $label)
    {
        return (new ClassesAnalyzer())->getClassInheritanceInfo($tokens, $startIndex, $label);
    }

    /**
     * @param int $startIndex
     * @param int $endIndex
     */
    private function makeClassyDefinitionSingleLine(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $endIndex; $i >= $startIndex; --$i) {
            if ($tokens[$i]->isWhitespace()) {
                $prevNonWhite = $tokens->getPrevNonWhitespace($i);
                $nextNonWhite = $tokens->getNextNonWhitespace($i);

                if ($tokens[$prevNonWhite]->isComment() || $tokens[$nextNonWhite]->isComment()) {
                    $content = $tokens[$prevNonWhite]->getContent();
                    if (!('#' === $content || '//' === substr($content, 0, 2))) {
                        $content = $tokens[$nextNonWhite]->getContent();
                        if (!('#' === $content || '//' === substr($content, 0, 2))) {
                            $tokens[$i] = new Token([T_WHITESPACE, ' ']);
                        }
                    }

                    continue;
                }

                if (!$tokens[$i - 1]->equals(',') && $tokens[$i + 1]->equalsAny([',', '(', ')']) || $tokens[$i - 1]->equals('(')) {
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

            if (!$tokens[$i]->isComment()) {
                continue;
            }

            if (!$tokens[$i + 1]->isWhitespace() && !$tokens[$i + 1]->isComment() && false === strpos($tokens[$i]->getContent(), "\n")) {
                $tokens->insertAt($i + 1, new Token([T_WHITESPACE, ' ']));
            }

            if (!$tokens[$i - 1]->isWhitespace() && !$tokens[$i - 1]->isComment()) {
                $tokens->insertAt($i, new Token([T_WHITESPACE, ' ']));
            }
        }
    }

    /**
     * @param int $startIndex
     * @param int $endIndex
     */
    private function makeClassyInheritancePartMultiLine(Tokens $tokens, $startIndex, $endIndex)
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
                if (false !== strpos($tokens[$j]->getContent(), "\n")) {
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
}
