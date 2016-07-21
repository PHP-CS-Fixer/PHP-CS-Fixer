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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\TokenizerLinter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TokenizerLinterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PhpCsFixer\Linter\TokenizerLinter::lintSource
     * @requires PHP 7.0
     */
    public function testLintSourceWithGoodCode()
    {
        $linter = new TokenizerLinter();
        $linter->lintSource('<?php echo 123;')->check(); // no exception should be raised
    }

    /**
     * @covers PhpCsFixer\Linter\TokenizerLinter::lintSource
     * @requires PHP 7.0
     *
     * @expectedException \PhpCsFixer\Linter\LintingException
     * @expectedExceptionMessageRegExp /syntax error, unexpected.*T_ECHO.*line 5/
     */
    public function testLintSourceWithBadCode()
    {
        $linter = new TokenizerLinter();
        $linter->lintSource('<?php
            print "line 2";
            print "line 3";
            print "line 4";
            echo echo;
        ')->check();
    }
}
