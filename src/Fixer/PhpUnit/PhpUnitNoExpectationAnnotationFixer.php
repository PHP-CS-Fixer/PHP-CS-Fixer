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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitNoExpectationAnnotationFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var bool
     */
    private $fixMessageRegExp;

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        $this->fixMessageRegExp = PhpUnitTargetVersion::fulfills($this->configuration['target'], PhpUnitTargetVersion::VERSION_4_3);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Usages of `@expectedException*` annotations MUST be replaced by `->setExpectedException*` methods.',
            [
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException FooException
     * @expectedExceptionMessageRegExp /foo.*$/
     * @expectedExceptionCode 123
     */
    function testAaa()
    {
        aaa();
    }
}
'
                ),
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException FooException
     * @expectedExceptionCode 123
     */
    function testBbb()
    {
        bbb();
    }

    /**
     * @expectedException FooException
     * @expectedExceptionMessageRegExp /foo.*$/
     */
    function testCcc()
    {
        ccc();
    }
}
',
                    ['target' => PhpUnitTargetVersion::VERSION_3_2]
                ),
            ],
            null,
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpUnitExpectationFixer.
     */
    public function getPriority(): int
    {
        return 10;
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('target', 'Target version of PHPUnit.'))
                ->setAllowedTypes(['string'])
                ->setAllowedValues([PhpUnitTargetVersion::VERSION_3_2, PhpUnitTargetVersion::VERSION_4_3, PhpUnitTargetVersion::VERSION_NEWEST])
                ->setDefault(PhpUnitTargetVersion::VERSION_NEWEST)
                ->getOption(),
            (new FixerOptionBuilder('use_class_const', 'Use ::class notation.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (!$tokens[$i]->isGivenKind(T_FUNCTION) || $tokensAnalyzer->isLambda($i)) {
                continue;
            }

            $functionIndex = $i;
            $docBlockIndex = $i;

            // ignore abstract functions
            $braceIndex = $tokens->getNextTokenOfKind($functionIndex, [';', '{']);
            if (!$tokens[$braceIndex]->equals('{')) {
                continue;
            }

            do {
                $docBlockIndex = $tokens->getPrevNonWhitespace($docBlockIndex);
            } while ($tokens[$docBlockIndex]->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_COMMENT]));

            if (!$tokens[$docBlockIndex]->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
            $annotations = [];

            foreach ($doc->getAnnotationsOfType([
                'expectedException',
                'expectedExceptionCode',
                'expectedExceptionMessage',
                'expectedExceptionMessageRegExp',
            ]) as $annotation) {
                $tag = $annotation->getTag()->getName();
                $content = $this->extractContentFromAnnotation($annotation);
                $annotations[$tag] = $content;
                $annotation->remove();
            }

            if (!isset($annotations['expectedException'])) {
                continue;
            }

            if (!$this->fixMessageRegExp && isset($annotations['expectedExceptionMessageRegExp'])) {
                continue;
            }

            $originalIndent = WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);

            $paramList = $this->annotationsToParamList($annotations);

            $newMethodsCode = '<?php $this->'
                .(isset($annotations['expectedExceptionMessageRegExp']) ? 'setExpectedExceptionRegExp' : 'setExpectedException')
                .'('
                .implode(', ', $paramList)
                .');';
            $newMethods = Tokens::fromCode($newMethodsCode);
            $newMethods[0] = new Token([
                T_WHITESPACE,
                $this->whitespacesConfig->getLineEnding().$originalIndent.$this->whitespacesConfig->getIndent(),
            ]);

            // apply changes
            $docContent = $doc->getContent();
            if ('' === $docContent) {
                $docContent = '/** */';
            }
            $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $docContent]);
            $tokens->insertAt($braceIndex + 1, $newMethods);

            $whitespaceIndex = $braceIndex + $newMethods->getSize() + 1;
            $tokens[$whitespaceIndex] = new Token([
                T_WHITESPACE,
                $this->whitespacesConfig->getLineEnding().$tokens[$whitespaceIndex]->getContent(),
            ]);

            $i = $docBlockIndex;
        }
    }

    private function extractContentFromAnnotation(Annotation $annotation): string
    {
        $tag = $annotation->getTag()->getName();

        if (1 !== Preg::match('/@'.$tag.'\s+(.+)$/s', $annotation->getContent(), $matches)) {
            return '';
        }

        $content = Preg::replace('/\*+\/$/', '', $matches[1]);

        if (Preg::match('/\R/u', $content)) {
            $content = Preg::replace('/\s*\R+\s*\*\s*/u', ' ', $content);
        }

        return rtrim($content);
    }

    /**
     * @param array<string, string> $annotations
     *
     * @return list<string>
     */
    private function annotationsToParamList(array $annotations): array
    {
        $params = [];
        $exceptionClass = ltrim($annotations['expectedException'], '\\');

        if (str_contains($exceptionClass, '*')) {
            $exceptionClass = substr($exceptionClass, 0, strpos($exceptionClass, '*'));
        }

        $exceptionClass = trim($exceptionClass);

        if (true === $this->configuration['use_class_const']) {
            $params[] = "\\{$exceptionClass}::class";
        } else {
            $params[] = "'{$exceptionClass}'";
        }

        if (isset($annotations['expectedExceptionMessage'])) {
            $params[] = var_export($annotations['expectedExceptionMessage'], true);
        } elseif (isset($annotations['expectedExceptionMessageRegExp'])) {
            $params[] = var_export($annotations['expectedExceptionMessageRegExp'], true);
        } elseif (isset($annotations['expectedExceptionCode'])) {
            $params[] = 'null';
        }

        if (isset($annotations['expectedExceptionCode'])) {
            $params[] = $annotations['expectedExceptionCode'];
        }

        return $params;
    }
}
