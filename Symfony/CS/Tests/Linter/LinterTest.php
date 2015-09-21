<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Linter\Linter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class LinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\CS\Linter\Linter::lintSource
     */
    public function testLintSourceWithGoodCode()
    {
        $linter = new Linter();
        $linter->lintSource('<?php echo 123;'); // no exception should be raised
    }

    /**
     * @covers Symfony\CS\Linter\Linter::lintSource
     *
     * @expectedException Symfony\CS\Linter\LintingException
     * @expectedExceptionMessageRegExp /syntax error, unexpected (?:'echo' \(T_ECHO\))|(?:T_ECHO)/
     */
    public function testLintSourceWithBadCode()
    {
        $linter = new Linter();
        $linter->lintSource('<?php echo echo;');
    }
}
