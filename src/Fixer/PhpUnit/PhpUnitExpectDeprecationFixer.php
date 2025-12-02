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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpUnitExpectDeprecationFixer extends AbstractPhpUnitFixer implements WhitespacesAwareFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Usages of `@expectedDeprecation` annotations MUST be replaced by `expectDeprecation()` method calls.',
            [
                new CodeSample(
                    <<<'PHP'
                        <?php
                        final class MyTest extends \PHPUnit_Framework_TestCase
                        {
                            /**
                             * @expectedDeprecation Deprecation message
                             */
                            public function testAaa()
                            {
                                aaa();
                            }
                        }

                        PHP
                ),
            ],
            null,
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer.
     */
    public function getPriority(): int
    {
        return 4;
    }

    public function isRisky(): bool
    {
        return true;
    }

    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex): void
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $endIndex - 1; $index > $startIndex; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION) || $tokensAnalyzer->isLambda($index)) {
                continue;
            }

            $braceIndex = $tokens->getNextTokenOfKind($index, [';', '{']);
            if (!$tokens[$braceIndex]->equals('{')) {
                continue;
            }

            $docBlockIndex = $index;
            do {
                $docBlockIndex = $tokens->getPrevNonWhitespace($docBlockIndex);
            } while ($tokens[$docBlockIndex]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT]));

            if (!$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }

            $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
            $deprecationMessages = [];

            foreach ($doc->getAnnotationsOfType(['expectedDeprecation']) as $annotation) {
                $content = $this->extractContentFromAnnotation($annotation);
                if ('' !== $content) {
                    $deprecationMessages[] = $content;
                }
                $annotation->remove();
            }

            if ([] === $deprecationMessages) {
                continue;
            }

            $originalIndent = WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);

            $docContent = $doc->getContent();
            if ('' === $docContent) {
                $docContent = '/** */';
            }
            $tokens[$docBlockIndex] = new Token([\T_DOC_COMMENT, $docContent]);

            $allMethodsCode = '<?php ';
            $isFirst = true;
            foreach ($deprecationMessages as $message) {
                if (!$isFirst) {
                    $allMethodsCode .= $this->whitespacesConfig->getLineEnding().$originalIndent.$this->whitespacesConfig->getIndent();
                }
                $allMethodsCode .= '$this->expectDeprecation('.var_export($message, true).');';
                $isFirst = false;
            }

            $newMethods = Tokens::fromCode($allMethodsCode);

            $newMethods[0] = new Token([
                \T_WHITESPACE,
                $this->whitespacesConfig->getLineEnding().$originalIndent.$this->whitespacesConfig->getIndent(),
            ]);

            $slices = [$braceIndex + 1 => []];
            foreach ($newMethods as $token) {
                $slices[$braceIndex + 1][] = $token;
            }

            $tokens->insertSlices($slices);

            $whitespaceIndex = $braceIndex + 1 + $newMethods->getSize();
            $tokens[$whitespaceIndex] = new Token([
                \T_WHITESPACE,
                $this->whitespacesConfig->getLineEnding().$tokens[$whitespaceIndex]->getContent(),
            ]);

            $index = $docBlockIndex;
        }
    }

    private function extractContentFromAnnotation(Annotation $annotation): string
    {
        if (!Preg::match('/@expectedDeprecation\s+(.+)$/s', $annotation->getContent(), $matches)) {
            return '';
        }

        $content = Preg::replace('/\*+\/$/', '', $matches[1]);

        if (Preg::match('/\R/u', $content)) {
            $content = Preg::replace('/\s*\R+\s*\*\s*/u', ' ', $content);
        }

        return rtrim($content);
    }
}
