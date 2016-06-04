<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Your name <your@email.com>
 */
final class ReplaceNamespaceFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replaces the namespace of the code to match the path.';
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_NAMESPACE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $oldNamespace = array();

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
            $nextToken = $tokens[$nextTokenIndex];
            $startIndex = $nextTokenIndex;

            while ($nextToken->equals(';') === false) {
                $oldNamespace[] = $nextToken->getContent();
                $nextTokenIndex = $tokens->getNextMeaningfulToken($nextTokenIndex);
                $nextToken = $tokens[$nextTokenIndex];
            }

            $tokens->clearRange($startIndex, $nextTokenIndex);

            break;
        }

        $tokens = Tokens::fromCode($tokens->generateCode());

        $startNamespace = array_shift($oldNamespace);
        $separator = array_shift($oldNamespace);

        $filePath = $file->getPath();

        $path = explode(DIRECTORY_SEPARATOR, $filePath);
        $size = count($path);

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $nextTokenIndex = $tokens->getNextNonWhitespace($index);
            $start = false;

            foreach ($path as $key => $value) {
                if ($start === false && $value !== $startNamespace) {
                    unset($path[$key]);
                    continue;
                } else {
                    $start = true;
                }

                $tokens->insertAt($nextTokenIndex - 1, $this->generateTokens($value));
                $nextTokenIndex = $tokens->getNextNonWhitespace($nextTokenIndex);

                if ($key < $size - 1) {
                    $tokens->insertAt($nextTokenIndex - 1, $this->generateTokens($separator));
                    $nextTokenIndex = $tokens->getNextNonWhitespace($nextTokenIndex);
                } else {
                    $tokens->insertAt($nextTokenIndex - 1, $this->generateTokens(';'));
                    $nextTokenIndex = $tokens->getNextNonWhitespace($nextTokenIndex);
                    break 2;
                }
            }
            break;
        }

        $tokens = Tokens::fromCode($tokens->generateCode());

        return $tokens->generateCode();
    }

    private function generateTokens($string)
    {
        // Start with dummy PHP code
        $tokens = Tokens::fromCode('<?php namespace '.$string.';');
        $tokens->clearRange(0, 2); // clear `<?php namespace `
        $tokens[count($tokens) - 1]->clear(); // clear `;`
        $tokens->clearEmptyTokens();

        return $tokens;
    }
}
