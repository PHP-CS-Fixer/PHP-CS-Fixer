<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Contrib;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class HeaderCommentFixerTest extends AbstractFixerTestCase
{
    protected static $testHeader = <<<'EOH'
This file is part of the PHP CS utility.

(c) Fabien Potencier <fabien@symfony.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOH;

    protected function getFixerConfiguration()
    {
        return array('header' => self::$testHeader);
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
        $this->doTest($expected, $input);
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
        $this->doTest($expected, $input);
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

        $this->doTest($expected, $input);
    }

    public function testFixRemovePreviousHeader()
    {
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

        $fixer = $this->getFixer();
        $fixer->configure(array('header' => ''));

        $this->doTest($expected, $input, null, $fixer);
    }

    public function testFixDoNotTouchFilesWithSeveralOpenTags()
    {
        $input = "<?php\nphpinfo();\n?>\n<?";
        $this->doTest($input);
    }

    public function testFixDoNotTouchFilesNotStartingWithOpenTag()
    {
        $input = " <?php\nphpinfo();\n";
        $this->doTest($input);
    }

    public function testFixDoNotTouchFilesWithInlineHtml()
    {
        $input = "<?php\nphpinfo();\n?><hr/>";
        $this->doTest($input);
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
        $this->doTest($expected, $input);
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage [header_comment] Header configuration is invalid. Expected "string", got "stdClass".
     */
    public function testInvalidConfig()
    {
        $fixer = $this->getFixer();
        $fixer->configure(array('header' => new \stdClass()));
    }
}
