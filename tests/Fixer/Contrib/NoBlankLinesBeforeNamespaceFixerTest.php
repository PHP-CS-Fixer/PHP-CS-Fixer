<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Contrib;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class NoBlankLinesBeforeNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     *
     * @param string      $expected
     * @param string|null $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideExamples()
    {
        return array(
            array("<?php\nnamespace X;"),
            array("<?php\nnamespace X;", "<?php\n\n\n\nnamespace X;"),
            array("<?php\r\nnamespace X;"),
            array("<?php\r\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;"),
            array("<?php\n\nnamespace\\Sub\\Foo::bar();"),
        );
    }

    public function testFixExampleWithComment()
    {
        $expected = <<<'EOF'
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\Contrib;
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

namespace PhpCsFixer\Fixer\Contrib;
EOF;

        $this->doTest($expected, $input);
    }
}
