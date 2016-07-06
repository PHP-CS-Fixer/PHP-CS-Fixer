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

namespace PhpCsFixer\Tests\Fixer\Comment;

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

    public function testFixSkipStrictDeclare()
    {
        $expected = <<<'EOH'
<?php declare ( strict_types = 1) ;

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
<?php declare ( strict_types = 1) ;

phpinfo();
EOH;

        $this->doTest($expected, $input);
    }

    public function testFixSkipStrictDeclareWithExistingComment()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

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
<?php declare(strict_types=1);

/*
 * Existing comment
 */

phpinfo();
EOH;

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

    /**
     * @dataProvider provideDoNotTouchCases
     */
    public function testDoNotTouch($expected)
    {
        $this->doTest($expected);
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
