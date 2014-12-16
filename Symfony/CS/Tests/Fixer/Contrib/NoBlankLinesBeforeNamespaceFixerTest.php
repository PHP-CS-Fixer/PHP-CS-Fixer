<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class NoBlankLinesBeforeNamespaceFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     *
     * @param string      $expected
     * @param string|null $input
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideExamples()
    {
        return array(

            // 'Happy path'
            array("<?php\nnamespace X;", "<?php\n\nnamespace X;"),

            // Multiple newlines
            array("<?php\nnamespace X;", "<?php\n\n\n\nnamespace X;"),

            // Windows-style newlines
            array("<?php\nnamespace X;", "<?php\n\rnamespace X;"),

            // No namespace
            array("<?php\nsome_code();\nsome_more_code();", "<?php\nsome_code();\nsome_more_code();"),

            // Don't change if there is a copyright notice
            array($this->getExampleWithComment(), $this->getExampleWithComment()),

        );
    }

    /**
     * @return string
     */
    private function getExampleWithComment()
    {
        return <<<EOF
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
EOF;
    }
}
