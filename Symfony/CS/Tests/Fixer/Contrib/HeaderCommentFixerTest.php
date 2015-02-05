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

class HeaderCommentFixerTest extends AbstractFixerTestBase
{
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
 class Foo()
 {
 }
EOH;

        $input = <<<'EOH'
<?php
/**
 * @author Antonio J. García Lagar <aj@garcialagar.es>
 */
 class Foo()
 {
 }
EOH;

        $this->makeTest($expected, $input);
    }

    protected function getFixer()
    {
        $header = <<<'EOH'
This file is part of the PHP CS utility.

(c) Fabien Potencier <fabien@symfony.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOH;

        $fixer = parent::getFixer();
        $config = $this->getMock('Symfony\CS\ConfigInterface');
        $config->expects($this->any())->method('getHeader')->will($this->returnValue($header));
        $fixer->setConfig($config);

        return $fixer;
    }
}
