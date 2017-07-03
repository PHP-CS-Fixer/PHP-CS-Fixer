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

namespace PhpCsFixer\Tests\Fixer\NamespaceNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractLinesBeforeNamespaceFixer
 * @covers \PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer
 */
final class SingleBlankLineBeforeNamespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideExamples
     *
     * @param string      $expected
     * @param null|string $input
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
        return [
            ["<?php\n\nnamespace X;"],
            ["<?php\n\nnamespace X;", "<?php\n\n\n\nnamespace X;"],
            ["<?php\r\n\r\nnamespace X;"],
            ["<?php\r\n\nnamespace X;", "<?php\r\n\r\n\r\n\r\nnamespace X;"],
            ["<?php\n\nfoo();\nnamespace\\bar\\baz();"],
        ];
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
