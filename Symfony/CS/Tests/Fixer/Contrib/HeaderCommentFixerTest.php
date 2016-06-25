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

use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;
use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class HeaderCommentFixerTest extends AbstractFixerTestBase
{
    protected static $savedHeader;
    protected static $testHeader = <<<EOH
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

    public function testFixDoNotTouchFilesWithSeveralOpenTags()
    {
        $input = "<?php\nphpinfo();\n?>\n<?";
        $this->makeTest($input);
    }

    public function testFixDoNotTouchFilesNotStartingWithOpenTag()
    {
        $input = " <?php\nphpinfo();\n";
        $this->makeTest($input);
    }

    public function testFixDoNotTouchFilesWithInlineHtml()
    {
        $input = "<?php\nphpinfo();\n?><hr/>";
        $this->makeTest($input);
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

    public function testFixAddHeaderFileWithNamespaceAtTop()
    {
        $expected = <<<'EOH'
<?php namespace Foo;

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


EOH;

        $input = "<?php namespace Foo;\n";
        $this->makeTest($expected, $input);
    }

    public function testFixAddHeaderFileWithNamespaceJustUnder()
    {
        $expected = <<<'EOH'
<?php
namespace Foo;

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


EOH;

        $input = "<?php\nnamespace Foo;\n";
        $this->makeTest($expected, $input);
    }

    public function testFixAddHeaderFileWithNamespaceBelow()
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

namespace Foo;

EOH;

        $input = "<?php\n\nnamespace Foo;\n";
        $this->makeTest($expected, $input);
    }
}
