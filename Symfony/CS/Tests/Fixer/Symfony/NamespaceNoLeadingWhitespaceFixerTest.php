<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Bram Gotink <bram@gotink.me>
 */
class NamespaceNoLeadingWhitespaceFixerTest extends AbstractFixerTestBase
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
            // long syntax tests
            array("<?php\nnamespace Test;"),
            array("<?php\n\nnamespace Test;"),
            array("<?php\nnamespace Test;", "<?php\n namespace Test;"),
            array('<?php namespace Test;'),
            array('<?php namespace Test;', '<?php  namespace Test;'),
        );
    }
}
