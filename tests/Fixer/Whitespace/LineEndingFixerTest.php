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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\LineEndingFixer
 */
final class LineEndingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        $cases = self::provideCommonCases();

        $cases[] = [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\n", // both cases
        ];

        $cases[] = [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \n |\r\nTEST;\r\n", // both cases
        ];

        $cases['T_INLINE_HTML'] = [
            "<?php ?>\nZ\r\n<?php ?>\nZ\r\n",
        ];

        $cases['!T_CONSTANT_ENCAPSED_STRING'] = [
            "<?php \$a=\"a\r\n\";",
        ];

        $cases[] = [
            "<?php echo 'foo',\n\n'bar';",
            "<?php echo 'foo',\r\r\n'bar';",
        ];

        $cases['T_CLOSE_TAG'] = [
            "<?php\n?>\n<?php\n",
            "<?php\n?>\r\n<?php\n",
        ];

        $cases['T_CLOSE_TAG II'] = [
            "<?php\n?>\n<?php\n?>\n<?php\n",
            "<?php\n?>\r\n<?php\n?>\r\n<?php\n",
        ];

        return $cases;
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield from array_map(static function (array $case): array {
            return array_reverse($case);
        }, self::provideCommonCases());

        yield [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\r\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\r\n", // both types
        ];

        yield [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\r\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \r\n |\nTEST;\n", // both types
        ];
    }

    private static function provideCommonCases(): array
    {
        return [
            'T_OPEN_TAG' => [
                "<?php\n \$a = 1;",
                "<?php\r\n \$a = 1;",
            ],
            'T_WHITESPACE' => [
                "<?php \n \$a\n= 1;\n",
                "<?php \r\n \$a\r\n= 1;\r\n",
            ],
            'T_COMMENT' => [
                "<?php /*\n*/",
                "<?php /*\r\n*/",
            ],
            'T_DOC_COMMENT' => [
                "<?php /**\n*/",
                "<?php /**\r\n*/",
            ],
            'T_START_HEREDOC' => [
                "<?php \$a = <<<'TEST'\nAA\nTEST;\n",
                "<?php \$a = <<<'TEST'\r\nAA\r\nTEST;\r\n",
            ],
            [
                "<?php \$a = <<<TEST\nAAA\nTEST;\n",
                "<?php \$a = <<<TEST\r\nAAA\r\nTEST;\r\n",
            ],
            'T_ENCAPSED_AND_WHITESPACE' => [
                "<?php \$a = <<<'TEST'\nAAAA 1\n \$b\nTEST;\n",
                "<?php \$a = <<<'TEST'\r\nAAAA 1\r\n \$b\r\nTEST;\r\n",
            ],
            [
                "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\n",
                "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\r\n",
            ],
        ];
    }
}
