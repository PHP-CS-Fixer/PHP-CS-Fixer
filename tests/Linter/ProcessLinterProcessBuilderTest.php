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

use PhpCsFixer\Linter\ProcessLinterProcessBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLinterProcessBuilder
 */
final class ProcessLinterProcessBuilderTest extends TestCase
{
    /**
     * @param string $executable
     * @param string $file
     * @param string $expected
     *
     * @testWith ["php", "foo.php", "\"php\" -l \"foo.php\""]
     *           ["C:\\Program Files\\php\\php.exe", "foo bar\\baz.php", "\"C:\\Program Files\\php\\php.exe\" -l \"foo bar\\baz.php\""]
     * @requires OS Linux|Darwin
     */
    public function testPrepareCommandOnPhpOnLinuxOrMac($executable, $file, $expected)
    {
        // @TODO drop condition at 2.4 (as 2.4 does not support HHVM)
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Skip tests for PHP compiler when running on HHVM compiler.');
        }

        $builder = new ProcessLinterProcessBuilder($executable);

        $this->assertSame(
            $expected,
            $builder->build($file)->getCommandLine()
        );
    }

    /**
     * @param string $executable
     * @param string $file
     * @param string $expected
     *
     * @testWith ["php", "foo.php", "\"php\" -l \"foo.php\""]
     *           ["C:\\Program Files\\php\\php.exe", "foo bar\\baz.php", "\"C:\\Program Files\\php\\php.exe\" -l \"foo bar\\baz.php\""]
     * @requires OS ^Win
     */
    public function testPrepareCommandOnPhpOnWindows($executable, $file, $expected)
    {
        $builder = new ProcessLinterProcessBuilder($executable);

        $this->assertSame(
            $expected,
            $builder->build($file)->getCommandLine()
        );
    }

    /**
     * @TODO drop condition at 2.4 (as 2.4 does not support HHVM)
     */
    public function testPrepareCommandOnHhvm()
    {
        if (!defined('HHVM_VERSION')) {
            $this->markTestSkipped('Skip tests for HHVM compiler when running on PHP compiler.');
        }

        $builder = new ProcessLinterProcessBuilder('hhvm');

        $this->assertSame(
            '"hhvm" --php -l "foo.php"',
            $builder->build('foo.php')->getCommandLine()
        );
    }
}
