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

use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Test\AccessibleObject;
use Symfony\Component\Process\ProcessUtils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class LinterTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareCommandOnPhp()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Skip tests for PHP compiler when running on HHVM compiler.');
        }

        $this->assertSame(
            $this->fixEscape('"php" -l "foo.php"'),
            AccessibleObject::create(new Linter('php'))->prepareCommand('foo.php')
        );

        $this->assertSame(
            $this->fixEscape('"C:\Program Files\php\php.exe" -l "foo bar\baz.php"'),
            AccessibleObject::create(new Linter('C:\Program Files\php\php.exe'))->prepareCommand('foo bar\baz.php')
        );
    }

    public function testPrepareCommandOnHhvm()
    {
        if (!defined('HHVM_VERSION')) {
            $this->markTestSkipped('Skip tests for HHVM compiler when running on PHP compiler.');
        }

        $this->assertSame(
            $this->fixEscape('"hhvm" --php -l "foo.php"'),
            AccessibleObject::create(new Linter('hhvm'))->prepareCommand('foo.php')
        );
    }

    /**
     * @covers PhpCsFixer\Linter\Linter::lintSource
     */
    public function testLintSourceWithGoodCode()
    {
        $linter = new Linter();
        $linter->lintSource('<?php echo 123;')->check(); // no exception should be raised
    }

    /**
     * @covers PhpCsFixer\Linter\Linter::lintSource
     *
     * @expectedException \PhpCsFixer\Linter\LintingException
     * @expectedExceptionMessageRegExp /syntax error, unexpected (?:'echo' \(T_ECHO\))|(?:T_ECHO)/
     */
    public function testLintSourceWithBadCode()
    {
        $linter = new Linter();
        $linter->lintSource('<?php echo echo;')->check();
    }

    /**
     * Fix escaping character.
     *
     * Escape character may be different on various environments.
     * This method change used escape character into character that is default
     * for environment.
     *
     * @param string $value          value to be fixed
     * @param string $usedEscapeChar used escape char, may be only ' or "
     *
     * @return string
     */
    private function fixEscape($value, $usedEscapeChar = '"')
    {
        static $escapeChar = null;

        if (null === $escapeChar) {
            $escapeChar = substr(ProcessUtils::escapeArgument('x'), 0, 1);
        }

        if ($usedEscapeChar === $escapeChar) {
            return $value;
        }

        return str_replace($usedEscapeChar, $escapeChar, $value);
    }
}
