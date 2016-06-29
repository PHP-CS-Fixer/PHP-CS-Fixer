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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class HeaderCommentFixerTest extends AbstractFixerTestBase
{
    protected static $savedHeader;
    protected static $testHeader = <<<'EOH'
This file is part of the PHP CS utility.

(c) Fabien Potencier <fabien@symfony.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOH;

    protected function setUp()
    {
        parent::setUp();
        self::$savedHeader = HeaderCommentFixer::getHeader();
        HeaderCommentFixer::setHeader(self::$testHeader);
    }

    protected function tearDown()
    {
        HeaderCommentFixer::setHeader(self::$savedHeader);
        parent::tearDown();
    }

    public function testFixWithPreviousHeader()
    {
        $expected = <<<'EOH'
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

phpinfo();
EOH;

        $input = <<<'EOH'
<?php



/*
 * Previous Header
 */

phpinfo();
EOH;
        $this->makeTest($expected, $input);
    }

    public function testFixWithoutPreviousHeader()
    {
        $expected = <<<'EOH'
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

phpinfo();
EOH;

        $input = <<<'EOH'
<?php



phpinfo();
EOH;
        $this->makeTest($expected, $input);
    }

    public function testFixWithClassDocblock()
    {
        $expected = <<<'EOH'
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class Foo
{
}
EOH;

        $input = <<<'EOH'
<?php
/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
class Foo
{
}
EOH;

        $this->makeTest($expected, $input);
    }

    public function testFixRemovePreviousHeader()
    {
        HeaderCommentFixer::setHeader('');
        $expected = <<<'EOH'
<?php

phpinfo();
EOH;

        $input = <<<'EOH'
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

phpinfo();
EOH;

        $this->makeTest($expected, $input);
    }

    public function testFixAddHeaderToEmptyFile()
    {
        $expected = <<<'EOH'
<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


EOH;

        $input = "<?php\n";
        $this->makeTest($expected, $input);
    }

    /**
     * @expectedException \Symfony\CS\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage [header_comment] Header configuration is invalid. Expected "string", got "stdClass".
     */
    public function testInvalidConfig()
    {
        HeaderCommentFixer::setHeader(new \stdClass());
    }

    /**
     * @dataProvider provideDoNotTouchCases
     */
    public function testDoNotTouch($expected)
    {
        $this->makeTest($expected);
    }

    public function provideDoNotTouchCases()
    {
        return array(
            array("<?php\nphpinfo();\n?>\n<?"),
            array(" <?php\nphpinfo();\n"),
            array("<?php\nphpinfo();\n?><hr/>"),
            array("  <?php\n"),
            array('<?= 1?>'),
            array('<?= 1?><?php'),
            array("<?= 1?>\n<?php"),
        );
    }
}
