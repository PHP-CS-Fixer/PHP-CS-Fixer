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
 * @author Graham Campbell <graham@mineuk.com>
 */
class SingleBlankLineBeforeNamespaceFixerTest extends AbstractFixerTestBase
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
            array("<?php\n\nnamespace X;"),
            array("<?php\n\nnamespace X;", "<?php\n\n\n\nnamespace X;"),
            array("<?php\r\n\r\nnamespace X;"),
            array("<?php\r\n\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;"),
            array("<?php\n\nfoo();\nnamespace\\bar\\baz();"),
        );
    }

    public function testFixExampleWithCommentTooMuch()
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

namespace Symfony\CS\Fixer\Contrib;

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

    public function testFixExampleWithCommentTooLittle()
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

namespace Symfony\CS\Fixer\Contrib;

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
