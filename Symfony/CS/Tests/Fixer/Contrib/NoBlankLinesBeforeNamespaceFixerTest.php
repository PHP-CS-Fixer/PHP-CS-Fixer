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
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array("<?php\nnamespace X;", "<?php\n\nnamespace X;"),
            array("<?php\nnamespace X;", "<?php\n\n\n\nnamespace X;"),
            array("<?php\nnamespace X;", "<?php\n\rnamespace X;"),
        );
    }
}
