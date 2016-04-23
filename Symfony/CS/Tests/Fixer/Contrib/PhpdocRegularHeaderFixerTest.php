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

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class PhpdocRegularHeaderFixerTest extends AbstractFixerTestBase
{
    public function testFixPhpStormHeader()
    {
        $expected = <<<'EOH'
<?php

/*
 * project
 * (c) some company
 *
 * User: John Doe
 * Date: 1-1-1978
 * Time: 12:34
 */

phpinfo();
EOH;

        $input = <<<'EOH'
<?php

/**
 * project
 * (c) some company
 *
 * User: John Doe
 * Date: 1-1-1978
 * Time: 12:34
 */

phpinfo();
EOH;
        $this->makeTest($expected, $input);
    }

    public function testIgnoreAnnotatedHeader()
    {
        $input = <<<'EOH'
<?php

/**
 * project
 * (c) some company
 *
 * User: John Doe
 * Date: 1-1-1978
 * Time: 12:34
 *
 * @author John Doe <john.doe@example.org>
 */

phpinfo();
EOH;
        $this->makeTest($input);
    }

    public function testIgnoreCorrectHeader()
    {
        $input = <<<'EOH'
<?php

/*
 * project
 * (c) some company
 *
 * User: John Doe
 * Date: 1-1-1978
 * Time: 12:34
 */

phpinfo();
EOH;
        $this->makeTest($input);
    }

    public function testFixDoNotTouchFilesNotStartingWithOpenTag()
    {
        $input = <<<'EOH'
<h1>Test</h1><?php

/*
 * project
 * (c) some company
 *
 * User: John Doe
 * Date: 1-1-1978
 * Time: 12:34
 */

phpinfo();
EOH;
        $this->makeTest($input);
    }
}
