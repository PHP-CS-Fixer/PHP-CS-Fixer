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

/**
 * @author Ben Harold <benharold@mac.com>
 */
class NamespaceFollowsOpeningTagFixerTest extends AbstractFixerTestBase
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
            array('<?php namespace X;'),
            array("<?php\n\nclass Foo {}"),
            array('<? namespace X;'),
            array('<? namespace X;', "<?\n\nnamespace X;"),
            array('<?= "output"; ?>'),
            array('<?php namespace X;', "<?php\n\n\n\nnamespace X;"),
            array('<?php namespace X;', "<?php\r\n\r\n\r\n\r\nnamespace X;"),
        );
    }

    public function testFixExampleWithComment()
    {
        $expected = <<<'EOF'
<?php namespace Symfony\CS\Fixer\Contrib;
EOF;

        $input = <<<'EOF'
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

        $this->makeTest($expected, $input);
    }
}
