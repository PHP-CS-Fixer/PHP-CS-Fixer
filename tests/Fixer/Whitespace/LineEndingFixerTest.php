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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\LineEndingFixer
 */
final class LineEndingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        $cases = $this->provideCommonCases();

        $cases[] = [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\n", // both cases
        ];

        $cases[] = [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \n |\r\nTEST;\r\n", // both cases
        ];

        // !T_INLINE_HTML
        $cases[] = [
            "<?php ?>\r\n<?php ?>\r\n",
        ];

        // !T_CONSTANT_ENCAPSED_STRING
        $cases[] = [
            "<?php \$a=\"a\r\n\";",
        ];

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        $cases = array_map(function (array $case) {
            return array_reverse($case);
        }, $this->provideCommonCases());

        $cases[] = [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\r\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \n |\nTEST;\r\n", // both types
        ];

        $cases[] = [
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\r\nAAAAA \r\n |\r\nTEST;\r\n",
            "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAAAAA \r\n |\nTEST;\n", // both types
        ];

        return $cases;
    }

    private function provideCommonCases()
    {
        return [
            // T_OPEN_TAG
            [
                "<?php\n \$a = 1;",
                "<?php\r\n \$a = 1;",
            ],
            // T_WHITESPACE
            [
                "<?php \n \$a\n= 1;\n",
                "<?php \r\n \$a\r\n= 1;\r\n",
            ],
            // T_COMMENT
            [
                "<?php /*\n*/",
                "<?php /*\r\n*/",
            ],
            // T_DOC_COMMENT
            [
                "<?php /**\n*/",
                "<?php /**\r\n*/",
            ],
            // T_START_HEREDOC
            [
                "<?php \$a = <<<'TEST'\nAA\nTEST;\n",
                "<?php \$a = <<<'TEST'\r\nAA\r\nTEST;\r\n",
            ],
            [
                "<?php \$a = <<<TEST\nAAA\nTEST;\n",
                "<?php \$a = <<<TEST\r\nAAA\r\nTEST;\r\n",
            ],
            // T_ENCAPSED_AND_WHITESPACE
            [
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
