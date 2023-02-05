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

namespace PhpCsFixer\Documentation;

/**
 * @internal
 */
final class CookbookGenerator
{
    private const TEMPLATES_PATH = __DIR__.'/../../dev-tools/cookbook/';

    public function generateDocumentation(): string
    {
        $fixerContent = file(self::TEMPLATES_PATH.'RemoveCommentsFixer.php');
        $testContent = file(self::TEMPLATES_PATH.'RemoveCommentsFixerTest.php');

        return strtr(
            file_get_contents(self::TEMPLATES_PATH.'template.rst'),
            [
                '{{ empty_fixer }}' => $this->excludeLines($fixerContent, [1 => 4, 19 => 20, 31 => 38, 40 => 41, 47 => 47, 53 => 70]),
                '{{ empty_test }}' => $this->excludeLines($testContent, [1 => 4, 37 => 37, 40 => 49, 51 => 57, 59 => 59]),
                '{{ test_no_change }}' => $this->excludeLines($testContent, [0 => 1, 4 => 36, 40 => 54, 60 => 60]),
                '{{ test_changes }}' => $this->excludeLines($testContent, [0 => 1, 4 => 36, 47 => 58, 60 => 60]),
                '{{ test }}' => $this->excludeLines($testContent, [1 => 1, 3 => 4, 24 => 25, 37 => 37, 47 => 59]),
                '{{ fixer_definition }}' => $this->excludeLines($fixerContent, [0 => 1, 4 => 25, 30 => 30, 40 => 71]),
                '{{ fixer_is_candidate }}' => $this->excludeLines($fixerContent, [0 => 1, 4 => 25, 28 => 40, 45 => 46, 49 => 71]),
                '{{ fixer_apply_fix_empty }}' => $this->excludeLines($fixerContent, [0 => 1, 4 => 25, 28 => 40, 42 => 48, 52 => 69]),
                '{{ fixer_apply_fix_partial }}' => $this->excludeLines($fixerContent, [0 => 1, 4 => 25, 28 => 40, 42 => 48, 52 => 52, 59 => 64, 67 => 71]),
                '{{ fixer_apply_fix }}' => $this->excludeLines($fixerContent, [0 => 1, 4 => 25, 28 => 40, 42 => 48, 52 => 52, 58 => 58, 67 => 71]),
                '{{ fixer }}' => $this->excludeLines($fixerContent, [1 => 4, 19 => 20, 30 => 30, 40 => 41, 45 => 46, 52 => 52, 58 => 58, 67 => 71]),
            ]
        );
    }

    /**
     * @param array<string>   $lines
     * @param array<int, int> $removeLines
     */
    private function excludeLines(array $lines, array $removeLines): string
    {
        foreach ($removeLines as $from => $to) {
            for ($i = $from; $i <= $to; ++$i) {
                unset($lines[$i]);
            }
        }

        $lines = array_map(
            fn (string $line): string => '' === trim($line) ? $line : '   '.$line,
            $lines
        );

        return ".. code-block:: php\n\n".rtrim(implode('', $lines));
    }
}
