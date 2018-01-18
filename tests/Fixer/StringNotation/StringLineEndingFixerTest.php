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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ilija Tovilo <ilija.tovilo@me.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\StringLineEndingFixer
 */
final class StringLineEndingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $template = "<?php\n\$a=\n<<<'EOT'\n%s\n\nEOT;\n";
        $input = '/**
* @SWG\Get(
*     path="/api/v0/cards",
*     operationId="listCards",
*     tags={"Банковские карты"},
*     summary="Возвращает список банковских карт."
*  )
*/';

        return [
            [
                "<?php \$a = 'my\nmulti\nline\nstring';\r\n",
                "<?php \$a = 'my\r\nmulti\nline\r\nstring';\r\n",
            ],
            [
                "<?php \$a = \"my\nmulti\nline\nstring\";\r\n",
                "<?php \$a = \"my\r\nmulti\nline\r\nstring\";\r\n",
            ],
            [
                "<?php \$a = \"my\nmulti\nline\nstring\nwith\n\$b\ninterpolation\";\r\n",
                "<?php \$a = \"my\r\nmulti\nline\r\nstring\nwith\r\n\$b\ninterpolation\";\r\n",
            ],
            [
                sprintf($template, $input),
                sprintf($template, str_replace("\n", "\r", $input)),
            ],
            [
                sprintf($template, $input),
                sprintf($template, str_replace("\n", "\r\n", $input)),
            ],
        ];
    }

    public function testWithDifferentLineEndingConfiguration()
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest(
            "<?php \$a = 'my\r\nmulti\r\nline\r\nstring';",
            "<?php \$a = 'my\nmulti\nline\nstring';"
        );
    }
}
