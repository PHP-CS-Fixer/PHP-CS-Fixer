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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitMockFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Usages of `->getMockWithoutInvokingTheOriginalConstructor` method MUST be replaced by `->createMock` method.',
            [
                new CodeSample(
                    '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function testFoo()
    {
        $mock = $this->getMockWithoutInvokingTheOriginalConstructor("Foo");
    }
}
'
                ),
            ],
            null,
            'Risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $phpUnitTestCaseIndicator = new PhpUnitTestCaseIndicator();

        $inPhpUnitClass = false;

        for ($index = 0, $limit = $tokens->count() - 1; $index < $limit; ++$index) {
            if (!$inPhpUnitClass && $tokens[$index]->isGivenKind(T_CLASS) && $phpUnitTestCaseIndicator->isPhpUnitClass($tokens, $index)) {
                $inPhpUnitClass = true;
            }

            if (!$inPhpUnitClass) {
                continue;
            }

            if (!$tokens[$index]->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            $index = $tokens->getNextMeaningfulToken($index);

            if ($tokens[$index]->equals([T_STRING, 'getMockWithoutInvokingTheOriginalConstructor'], false)) {
                $tokens[$index] = new Token([T_STRING, 'createMock']);
            }
        }
    }
}
