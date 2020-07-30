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

namespace PhpCsFixer\Priority;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @internal
 */
final class PriorityFixer extends AbstractFixer
{
    public function getName()
    {
        return 'Internal/'.strtolower(str_replace('\\', '_', Utils::camelCaseToUnderscore(__CLASS__)));
    }

    public function getPriority()
    {
        return -100;
    }

    public function supports(\SplFileInfo $file)
    {
        return false !== strpos($file->getPath(), 'src'.\DIRECTORY_SEPARATOR.'Fixer'.\DIRECTORY_SEPARATOR);
    }

    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    public function getDefinition()
    {
        return new FixerDefinition(
            'Priorities calculated by PrioritiesCalculator must be used.',
            [
                new CodeSample(
                    '<?php class NonPrintableCharacterFixer extends AbstractFixer {
                    public function getPriority() { return -100; }
                    }'
                ),
            ]
        );
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $priority = $this->findPriority($tokens);
        if (null === $priority) {
            return;
        }

        $range = $this->findRangeToOverride($tokens);
        if (null === $range) {
            return;
        }
        list($priorityStartIndex, $priorityEndIndex) = $range;

        $tokens->overrideRange($priorityStartIndex, $priorityEndIndex, [new Token([T_LNUMBER, (string) $priority])]);
    }

    /**
     * @return null|int
     */
    private function findPriority(Tokens $tokens)
    {
        $indices = $tokens->findSequence([[T_EXTENDS]]);

        if (null === $indices) {
            return null;
        }

        /** @var int $sequencesStartIndex */
        $sequencesStartIndex = key($indices);

        /** @var int $classNameIndex */
        $classNameIndex = $tokens->getPrevMeaningfulToken($sequencesStartIndex);

        /** @var Token $classNameToken */
        $classNameToken = $tokens[$classNameIndex];

        $className = $classNameToken->getContent();

        $nameParts = explode('\\', $className);
        $name = substr(end($nameParts), 0, -\strlen('Fixer'));

        $name = Utils::camelCaseToUnderscore($name);

        static $priorities;

        if (null === $priorities) {
            $prioritiesCalculator = new PrioritiesCalculator();
            $priorities = $prioritiesCalculator->calculate();
        }

        if (!isset($priorities[$name])) {
            return null;
        }

        return $priorities[$name];
    }

    /**
     * @return null|int[]
     */
    private function findRangeToOverride(Tokens $tokens)
    {
        $indices = $tokens->findSequence([[T_PUBLIC], [T_FUNCTION], [T_STRING, 'getPriority']]);

        if (null === $indices) {
            return null;
        }

        /** @var int $sequencesStartIndex */
        $sequencesStartIndex = key($indices);

        /** @var int $returnIndex */
        $returnIndex = $tokens->getNextTokenOfKind($sequencesStartIndex, [[T_RETURN]]);

        $priorityStartIndex = $returnIndex + 2;

        /** @var Token $priorityStartToken */
        $priorityStartToken = $tokens[$priorityStartIndex];

        if ($priorityStartToken->isGivenKind(T_VARIABLE)) {
            return null;
        }

        /** @var int $nextIndex */
        $nextIndex = $tokens->getNextTokenOfKind($priorityStartIndex, [';']);

        $priorityEndIndex = $nextIndex - 1;

        return [$priorityStartIndex, $priorityEndIndex];
    }
}
