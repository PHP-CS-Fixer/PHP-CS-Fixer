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

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
class LinefeedFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            // T_OPEN_TAG
            array(
                "<?php\n \$a = 1;",
                "<?php\r\n \$a = 1;",
            ),
            // T_WHITESPACE
            array(
                "<?php \n \$a\n= 1;\n",
                "<?php \r\n \$a\r\n= 1;\r\n",
            ),
            // T_COMMENT
            array(
                "<?php /*\n*/",
                "<?php /*\r\n*/",
            ),
            // T_DOC_COMMENT
            array(
                "<?php /**\n*/",
                "<?php /**\r\n*/",
            ),
            // T_START_HEREDOC
            array(
                "<?php \$a = <<<'TEST'\nAA\nTEST;\n",
                "<?php \$a = <<<'TEST'\r\nAA\nTEST;\n",
            ),
            array(
                "<?php \$a = <<<TEST\nAA\nTEST;\n",
                "<?php \$a = <<<TEST\r\nAA\nTEST;\n",
            ),
            // T_ENCAPSED_AND_WHITESPACE
            array(
                "<?php \$a = <<<'TEST'\nAA 1\r\n \$b\nTEST;\n",
                "<?php \$a = <<<'TEST'\r\nAA 1\r\n \$b\r\nTEST;\n",
            ),
            array(
                "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAA \r\n |\nTEST;\n",
                "<?php \$b = \" \$a \r\n 123\"; \$a = <<<TEST\nAA \r\n |\r\nTEST;\n",
            ),
            // !T_INLINE_HTML
            array(
                "<?php ?>\r\n<?php ?>\r\n",
            ),
            // !T_CONSTANT_ENCAPSED_STRING
            array(
                "<?php \$a=\"a\r\n\";",
            ),
        );
    }
}
