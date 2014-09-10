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
            // with newline
            array("<?php\nnamespace Test;"),
            array("<?php\n\nnamespace Test;"),
            array("<?php\nnamespace Test;", "<?php\n namespace Test;"),
            // without newline
            array('<?php namespace Test;'),
            array('<?php namespace Test;', '<?php  namespace Test;'),
            // multiple namespaces with newline
            array("<?php\nnamespace Test1;\nnamespace Test2;"),
            array("<?php\nnamespace Test1;\nnamespace Test2;", "<?php\n namespace Test1;\n   namespace Test2;"),
            array("<?php\nnamespace Test1;\nclass Test {}\nnamespace Test2;", "<?php\n namespace Test1;\nclass Test {}\n   namespace Test2;"),
            array("<?php\nnamespace Test1;\nuse Exception;\nnamespace Test2;", "<?php\n namespace Test1;\nuse Exception;\n   namespace Test2;"),
            // multiple namespaces without newline
            array('<?php namespace Test1; namespace Test2;'),
            array('<?php namespace Test1; namespace Test2;', '<?php    namespace Test1;  namespace Test2;'),
            array('<?php namespace Test1; namespace Test2;', '<?php namespace Test1;  namespace Test2;'),
        );
    }
}
