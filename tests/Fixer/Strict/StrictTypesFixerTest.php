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

namespace PhpCsFixer\Tests\Fixer\Strict;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @internal
 */
final class StrictTypesFixerTest extends AbstractFixerTestCase
{
    public function testFixAlreadyThere()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

phpinfo();
EOH;

        $input = <<<'EOH'
<?php

declare(strict_types = 1);

phpinfo();
EOH;
        $this->doTest($expected, $input);
    }

    public function testSkipValidDeclare()
    {
        $input = <<<'EOH'
<?php declare(strict_types=1);

phpinfo();
EOH;

        $this->doTest($input);
    }

    public function testFixAddsCorrectly()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

phpinfo();
EOH;

        $input = <<<'EOH'
<?php phpinfo();
EOH;
        $this->doTest($expected, $input);
    }

    public function testFixAddsCorrectly2()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

phpinfo();
EOH;

        $input = <<<'EOH'
<?php

phpinfo();
EOH;
        $this->doTest($expected, $input);
    }

    public function testFixAddsToEmptyFile()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);


EOH;

        $input = "<?php\n";
        $this->doTest($expected, $input);
    }

    public function testFixAddsAboveSingleLineComment()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

// foo
EOH;

        $input = <<<'EOH'
<?php
// foo
EOH;
        $this->doTest($expected, $input);
    }

    public function testFixAddsAbovePHPDoc()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

/**
 * Foo
 */
phpinfo();
EOH;

        $input = <<<'EOH'
<?php

/**
 * Foo
 */
phpinfo();
EOH;
        $this->doTest($expected, $input);
    }

    public function testFixAddsAboveComment()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

/*
 * Foo
 */

phpinfo();
EOH;

        $input = <<<'EOH'
<?php

/*
 * Foo
 */

phpinfo();
EOH;
        $this->doTest($expected, $input);
    }

    public function testFixAddsAboveInlineComment()
    {
        $expected = <<<'EOH'
<?php declare(strict_types=1);

/* Foo */
// Bla
phpinfo();
EOH;

        $input = <<<'EOH'
<?php /* Foo */
// Bla
phpinfo();
EOH;
        $this->doTest($expected, $input);
    }

    public function testFixDoNotTouchShortOpenTag()
    {
        $input = <<<'EOH'
<?

phpinfo();
EOH;
        $this->doTest($input);
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
}
