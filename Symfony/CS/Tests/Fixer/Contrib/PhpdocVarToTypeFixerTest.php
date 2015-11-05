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
 * @author Graham Campbell <graham@mineuk.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class PhpdocVarToTypeFixerTest extends AbstractFixerTestBase
{
    public function testBasicDoc()
    {
        $this->makeTest('<?php /** @type string Hello! */', '<?php /** @var string Hello! */');
    }

    public function testEmptyDoc()
    {
        $this->makeTest("<?php\n    /**\n     *\n     */\n");
    }

    public function testInlineDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @type bool   $required Whether this element is required
     *     @type string $label    The display name for this element
     * }
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF;

        $this->makeTest($expected, $input);
    }
}
